<?php

namespace App\Controllers;

use App\Models\PengaduanModel;
use App\Models\PengaduanTahapanModel;
use App\Models\AuditTrailModel;

class AdminController extends BaseController
{
    protected $pengaduanModel;
    protected $tahapanModel;
    protected $auditModel;

    public function __construct()
    {
        $this->pengaduanModel = new PengaduanModel();
        $this->tahapanModel   = new PengaduanTahapanModel();
        $this->auditModel     = new AuditTrailModel();
    }

    // Menampilkan halaman detail untuk admin
    public function detailTiket($nomorTiket)
    {
        $pengaduan = $this->pengaduanModel->getByNomorTiket($nomorTiket);
        if (!$pengaduan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'pengaduan' => $pengaduan,
            'tahapan'   => $this->tahapanModel->getByPengaduan($pengaduan['id']),
            'audit'     => $this->auditModel->where('pengaduan_id', $pengaduan['id'])->findAll()
        ];

        return view('admin/detail_pengaduan', $data);
    }

    // Memproses perubahan SLA dari form
    public function updateSla()
    {
        $tahapanId   = $this->request->getPost('tahapan_id');
        $pengaduanId = $this->request->getPost('pengaduan_id');
        $slaBaru     = (int) $this->request->getPost('sla_hari');
        $namaTahap   = $this->request->getPost('nama_tahap');
        $slaLama     = $this->request->getPost('sla_lama');

        // 1. Update SLA di tabel pengaduan_tahapan
        $this->tahapanModel->update($tahapanId, ['sla_hari' => $slaBaru]);

        // 2. Catat ke Audit Trail (SANGAT PENTING)
        $pesanAudit = "Admin mengubah batas waktu SLA pada tahap '{$namaTahap}' dari {$slaLama} hari menjadi {$slaBaru} hari.";
        
        $this->auditModel->insert([
            'pengaduan_id' => $pengaduanId,
            'aktivitas'    => $pesanAudit,
            'oleh'         => 'Admin' // Nanti ubah dengan session nama admin jika fitur login sudah ada
        ]);

        return redirect()->back()->with('success', 'SLA berhasil diperbarui dan dicatat di riwayat.');
    }
}