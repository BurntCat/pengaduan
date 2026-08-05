<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaduanModel extends Model
{
    protected $table            = 'pengaduan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nomor_tiket',
        'kategori_id',
        'lokasi_id',
        'kronologi',
        'nama_pelapor',
        'email_pelapor',
        'no_hp_pelapor',
        'rahasiakan_identitas',
        'status_akhir',
        'alasan_penolakan'
    ];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil detail pengaduan lengkap (kategori + lokasi) berdasarkan nomor tiket.
     */
    public function getByNomorTiket(string $nomorTiket)
    {
        return $this->select('pengaduan.*, kategori_pengaduan.nama_kategori, lokasi_kejadian.nama_lokasi, lokasi_kejadian.kode_wilayah')
                    ->join('kategori_pengaduan', 'kategori_pengaduan.id = pengaduan.kategori_id')
                    ->join('lokasi_kejadian', 'lokasi_kejadian.id = pengaduan.lokasi_id')
                    ->where('pengaduan.nomor_tiket', $nomorTiket)
                    ->first();
    }

    /**
     * Generate nomor tiket format: {KODE_WILAYAH}-{TAHUN}-{5 digit urut}
     * Contoh: JATIM-2026-00125
     */
    public function generateNomorTiket(string $kodeWilayah): string
    {
        $tahun = date('Y');
        $prefix = strtoupper($kodeWilayah) . '-' . $tahun . '-';

        $last = $this->like('nomor_tiket', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        if ($last) {
            $lastNumber = (int) substr($last['nomor_tiket'], strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
