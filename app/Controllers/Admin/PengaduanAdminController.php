<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaduanModel;
use App\Models\PengaduanTahapanModel;
use App\Models\AuditTrailModel;

class PengaduanAdminController extends BaseController
{
    protected PengaduanModel $pengaduanModel;
    protected PengaduanTahapanModel $tahapanModel;
    protected AuditTrailModel $auditModel;

    public function __construct()
    {
        $this->pengaduanModel = new PengaduanModel();
        $this->tahapanModel   = new PengaduanTahapanModel();
        $this->auditModel     = new AuditTrailModel();
    }

    /**
     * GET /admin/pengaduan
     * Daftar seluruh pengaduan yang masuk
     */
    public function index()
    {
        $data['title'] = 'Kelola Pengaduan';
        $data['daftar'] = $this->pengaduanModel
            ->select('pengaduan.*, kategori_pengaduan.nama_kategori')
            ->join('kategori_pengaduan', 'kategori_pengaduan.id = pengaduan.kategori_id')
            // Tambahkan filter ini agar yang berstatus 'ditolak' tidak muncul di list utama
            ->where('pengaduan.status_akhir !=', 'ditolak')
            ->orderBy('pengaduan.created_at', 'DESC')
            ->findAll();

        return view('admin/pengaduan_list', $data);
    }

    /**
     * GET /admin/pengaduan/{nomor_tiket}
     * Form untuk admin mengatur SLA & status tiap tahap
     */
    public function edit(string $nomorTiket)
    {
        $pengaduan = $this->pengaduanModel->getByNomorTiket($nomorTiket);

        if (! $pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Nomor tiket tidak ditemukan.');
        }

        $data = [
            'title'     => 'Atur SLA - ' . $nomorTiket,
            'pengaduan' => $pengaduan,
            'tahapan'   => $this->tahapanModel->getByPengaduan((int) $pengaduan['id']),
            'labelTahap' => PengaduanTahapanModel::LABEL,
        ];

        return view('admin/pengaduan_sla', $data);
    }

/**
     * POST /admin/pengaduan/{nomor_tiket}/simpan
     * Menyimpan perubahan SLA (hari) dan status tiap tahap dengan pengaman tahap pertama
     */
    public function simpan(string $nomorTiket)
    {
        $pengaduan = $this->pengaduanModel->getByNomorTiket($nomorTiket);

        if (! $pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Nomor tiket tidak ditemukan.');
        }

        $slaInput    = $this->request->getPost('sla') ?? [];
        $statusInput = $this->request->getPost('status') ?? [];

        // Lakukan perulangan berdasarkan data input SLA
        foreach ($slaInput as $tahapanId => $slaHari) {
            // Ambil data tahapan dari database untuk memeriksa posisi/jenis tahapannya
            $tahapanData = $this->tahapanModel->find($tahapanId);

            if ($tahapanData) {
                // ATURAN PATEN: Jika ini tahap pertama ('diterima' atau urutan 1), 
                // statusnya wajib 'selesai' dan tidak boleh diubah oleh input luar.
                if ($tahapanData['tahap'] === 'diterima' || $tahapanData['urutan'] == 1) {
                    $statusFinal = 'selesai';
                } else {
                    // Ambil status dari input form, default ke 'menunggu' jika kosong
                    $statusFinal = $statusInput[$tahapanId] ?? 'menunggu';
                }

                // 1. Update jumlah hari SLA
                $this->tahapanModel->updateSlaTahap((int) $tahapanId, (int) $slaHari);

                // 2. Update status dengan status yang sudah diamankan
                $this->tahapanModel->ubahStatusTahap((int) $tahapanId, $statusFinal);
            }
        }

        // Catat ke audit trail
        $this->auditModel->catat(
            (int) $pengaduan['id'],
            'SLA dan status tahapan diperbarui oleh admin.',
            'Admin'
        );

        return redirect()->to('/admin/pengaduan/' . $nomorTiket)
                        ->with('success', 'Perubahan SLA berhasil disimpan.');
    }

    public function status($nomorTiket)
    {
        // 1. Cari tiket berdasarkan nomor resi
        $pengaduan = $this->pengaduanModel->where('nomor_tiket', $nomorTiket)->first();
        
        if (!$pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Tiket tidak ditemukan.");
        }

        // 2. Ambil tahapan dan hitung total SLA dari database (bukan hardcode)
        $tahapan = $this->tahapanModel->getByPengaduan($pengaduan['id']);
        
        $totalSla = 0;
        foreach ($tahapan as $t) {
            $totalSla += $t['sla_hari'];
        }

        // 3. Ambil riwayat aktivitas
        $audit = $this->auditModel->where('pengaduan_id', $pengaduan['id'])
                                  ->orderBy('created_at', 'DESC')
                                  ->findAll();

        $data = [
            'pengaduan' => $pengaduan,
            'tahapan'   => $tahapan,
            'totalSla'  => $totalSla,
            'audit'     => $audit
        ];

        // Arahkan ke file View milikmu yang ada di screenshot
        return view('pengaduan/status', $data); 
    }
/**
     * GET /admin/pengaduan/{nomor_tiket}/form-tolak
     * Menampilkan halaman form khusus bagi admin untuk mengisi alasan penolakan
     */
    public function formTolak(string $nomorTiket)
    {
        $pengaduan = $this->pengaduanModel->getByNomorTiket($nomorTiket);

        if (! $pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Nomor tiket tidak ditemukan.');
        }

        $data = [
            'title'     => 'Alasan Penolakan - ' . $nomorTiket,
            'pengaduan' => $pengaduan,
        ];

        return view('admin/pengaduan_tolak_form', $data);
    }

    /**
     * POST /admin/pengaduan/{nomor_tiket}/tolak
     * Menyimpan status ditolak beserta alasannya ke database
     */
    public function tolak(string $nomorTiket)
    {
        $pengaduan = $this->pengaduanModel->getByNomorTiket($nomorTiket);

        if (! $pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Nomor tiket tidak ditemukan.');
        }

        $alasan = $this->request->getPost('alasan_penolakan');

        if (empty($alasan)) {
            return redirect()->back()->withInput()->with('error', 'Alasan penolakan wajib diisi.');
        }

        // Lakukan update langsung menggunakan instance model berdasarkan ID
        $this->pengaduanModel->update((int) $pengaduan['id'], [
            'status_akhir'     => 'ditolak',
            'alasan_penolakan' => trim($alasan)
        ]);

        // Catat ke Audit Trail
        $this->auditModel->catat(
            (int) $pengaduan['id'],
            'Pengaduan ditolak. Alasan: ' . $alasan,
            'Admin'
        );

        return redirect()->to('/admin/pengaduan')
                        ->with('success', 'Tiket pengaduan berhasil ditolak dengan catatan alasan.');
    }
}
