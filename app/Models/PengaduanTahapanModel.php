<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaduanTahapanModel extends Model
{
    protected $table         = 'pengaduan_tahapan';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'pengaduan_id',
        'tahap',
        'urutan',
        'status',
        'sla_hari',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];
    protected $useTimestamps = false;

    public const LABEL = [
        'diterima'         => 'Diterima',
        'diverifikasi'     => 'Diverifikasi',
        'ditangani_bidang' => 'Ditangani Bidang',
        'selesai'          => 'Selesai',
    ];

    /**
     * Buat 4 baris tahapan untuk pengaduan baru, SLA diambil dari sla_default
     * milik kategori terkait (SLA ini nanti bisa diubah admin per tiket).
     */
    public function buatTahapanAwal(int $pengaduanId, int $kategoriId): void
    {
        $slaDefaultModel = new SlaDefaultModel();
        $slaList = $slaDefaultModel->getByKategori($kategoriId);

        $now = date('Y-m-d H:i:s');

        foreach ($slaList as $sla) {
            $this->insert([
                'pengaduan_id'    => $pengaduanId,
                'tahap'           => $sla['tahap'],
                'urutan'          => $sla['urutan'],
                'sla_hari'        => $sla['sla_hari'],
                // tahap pertama (Diterima) langsung berstatus selesai saat submit
                'status'          => $sla['urutan'] == 1 ? 'selesai' : 'menunggu',
                'tanggal_mulai'   => $sla['urutan'] == 1 ? $now : null,
                'tanggal_selesai' => $sla['urutan'] == 1 ? $now : null,
            ]);
        }
    }

    public function getByPengaduan(int $pengaduanId): array
    {
        return $this->where('pengaduan_id', $pengaduanId)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    public function updateSlaTahap(int $id, int $slaHari): bool
    {
        return $this->update($id, ['sla_hari' => $slaHari]);
    }

    public function ubahStatusTahap(int $id, string $status): bool
    {
        $data = ['status' => $status];
        if ($status === 'proses') {
            $data['tanggal_mulai'] = date('Y-m-d H:i:s');
        } elseif ($status === 'selesai') {
            $data['tanggal_selesai'] = date('Y-m-d H:i:s');
        }
        return $this->update($id, $data);
    }
}
