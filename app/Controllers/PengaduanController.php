<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\LokasiModel;
use App\Models\PengaduanModel;
use App\Models\PengaduanTahapanModel;
use App\Models\LampiranModel;
use App\Models\AuditTrailModel;

class PengaduanController extends BaseController
{
    protected KategoriModel $kategoriModel;
    protected LokasiModel $lokasiModel;
    protected PengaduanModel $pengaduanModel;
    protected PengaduanTahapanModel $tahapanModel;
    protected LampiranModel $lampiranModel;
    protected AuditTrailModel $auditModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->lokasiModel   = new LokasiModel();
        $this->pengaduanModel = new PengaduanModel();
        $this->tahapanModel   = new PengaduanTahapanModel();
        $this->lampiranModel  = new LampiranModel();
        $this->auditModel     = new AuditTrailModel();
    }

    /**
     * GET /pengaduan
     * Menampilkan Form Pengaduan (sesuai gambar 1)
     */
    public function index()
    {
        $data = [
            'title'    => 'Form Pengaduan',
            'kategori' => $this->kategoriModel->getActive(),
            'lokasi'   => $this->lokasiModel->getActive(),
        ];

        return view('pengaduan/form', $data);
    }

    /**
     * POST /pengaduan/kirim
     * Memproses submit form, generate nomor tiket, lalu redirect ke halaman status
     */
    public function kirim()
    {
        $rules = [
            'kategori_id'  => 'required|is_natural_no_zero',
            'lokasi_id'    => 'required|is_natural_no_zero',
            'kronologi'    => 'required|min_length[10]',
            'nama_pelapor' => 'required|min_length[3]|max_length[150]',
            'bukti'        => 'max_size[bukti,10240]|ext_in[bukti,pdf,jpg,jpeg,png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lokasi = $this->lokasiModel->find($this->request->getPost('lokasi_id'));
        $kodeWilayah = $lokasi['kode_wilayah'] ?? 'UMUM';

        $nomorTiket = $this->pengaduanModel->generateNomorTiket($kodeWilayah);

        $rahasiakan = $this->request->getPost('rahasiakan_identitas') ? 1 : 0;

        $pengaduanId = $this->pengaduanModel->insert([
            'nomor_tiket'          => $nomorTiket,
            'kategori_id'          => $this->request->getPost('kategori_id'),
            'lokasi_id'            => $this->request->getPost('lokasi_id'),
            'kronologi'            => $this->request->getPost('kronologi'),
            'nama_pelapor'         => $this->request->getPost('nama_pelapor'),
            'email_pelapor'        => $this->request->getPost('email_pelapor'),
            'no_hp_pelapor'        => $this->request->getPost('no_hp_pelapor'),
            'rahasiakan_identitas' => $rahasiakan,
            'status_akhir'         => 'diterima',
        ]);

        // Buat 4 tahapan awal (SLA diambil dari SLA default kategori, admin bisa ubah nanti)
        $this->tahapanModel->buatTahapanAwal(
            (int) $pengaduanId,
            (int) $this->request->getPost('kategori_id')
        );

        // Upload lampiran bukti (bisa multiple file jika input diubah menjadi bukti[])
        $files = $this->request->getFileMultiple('bukti') ?? [$this->request->getFile('bukti')];
        foreach ($files as $file) {
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . '../public/uploads/bukti_pengaduan', $newName);

                $this->lampiranModel->insert([
                    'pengaduan_id' => $pengaduanId,
                    'nama_file'    => $file->getClientName(),
                    'path_file'    => 'uploads/bukti_pengaduan/' . $newName,
                    'mime_type'    => $file->getClientMimeType(),
                    'ukuran_kb'    => (int) ($file->getSize() / 1024),
                ]);
            }
        }

        $this->auditModel->catat((int) $pengaduanId, 'Pengaduan diterima dan tiket ' . $nomorTiket . ' diterbitkan.');

        return redirect()->to('/pengaduan/status/' . $nomorTiket)
                          ->with('success', 'Aduan Anda berhasil dikirim.');
    }

    /**
     * GET /pengaduan/status/{nomor_tiket}
     * Menampilkan halaman tracking nomor tiket (sesuai gambar 2)
     */
    public function status(string $nomorTiket)
    {
        $pengaduan = $this->pengaduanModel->getByNomorTiket($nomorTiket);

        if (! $pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Nomor tiket tidak ditemukan.');
        }

        if ($pengaduan['status_akhir'] === 'ditolak') {
            $data = [
                'title'     => 'Tiket Ditolak',
                'pengaduan' => $pengaduan,
            ];
            // Pastikan kamu membuat file view ini (misal: pengaduan/ditolak.php)
            return view('pengaduan/ditolak', $data);
        }
        
        $tahapan = $this->tahapanModel->getByPengaduan((int) $pengaduan['id']);
        $audit   = $this->auditModel->getByPengaduan((int) $pengaduan['id']);

        // Total SLA maksimal = jumlah SLA seluruh tahap
        $totalSla = array_sum(array_column($tahapan, 'sla_hari'));

        $data = [
            'title'     => 'Status Pengaduan',
            'pengaduan' => $pengaduan,
            'tahapan'   => $tahapan,
            'audit'     => $audit,
            'totalSla'  => $totalSla,
            'labelTahap' => PengaduanTahapanModel::LABEL,
        ];

        return view('pengaduan/tiket', $data);
    }

    // Menampilkan form input email/no_hp untuk melacak riwayat
    public function cariRiwayat()
    {
        return view('pengaduan/form_riwayat', [
            'title' => 'Cek Riwayat Pengaduan'
        ]);
    }

    // Memproses pencarian data ke database
    public function prosesCariRiwayat()
    {
        $keyword = $this->request->getPost('keyword'); // Bisa berupa email atau no HP

        if (empty($keyword)) {
            return redirect()->back()->with('error', 'Masukkan email atau nomor HP Anda.');
        }

        // Cari semua pengaduan yang cocok dengan email atau no HP pelapor
        $riwayat = $this->pengaduanModel->where('email_pelapor', $keyword)
                                       ->orWhere('no_hp_pelapor', $keyword)
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll();

        $data = [
            'title'   => 'Hasil Riwayat Pengaduan',
            'keyword' => $keyword,
            'riwayat' => $riwayat
        ];

        return view('pengaduan/hasil_riwayat', $data);
    }
    // Menampilkan seluruh list riwayat pengaduan secara langsung tanpa filter
    public function riwayatSemua()
    {
        // Mengambil semua data pengaduan, diurutkan dari yang paling baru
        $riwayat = $this->pengaduanModel->orderBy('created_at', 'DESC')->findAll();

        return view('pengaduan/riwayat_list', [
            'title'   => 'Daftar Seluruh Pengaduan',
            'riwayat' => $riwayat
        ]);
    }
}
