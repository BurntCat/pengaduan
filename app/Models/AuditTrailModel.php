<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditTrailModel extends Model
{
    protected $table         = 'pengaduan_audit_trail';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['pengaduan_id', 'aktivitas', 'oleh'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function catat(int $pengaduanId, string $aktivitas, string $oleh = 'Sistem'): void
    {
        $this->insert([
            'pengaduan_id' => $pengaduanId,
            'aktivitas'    => $aktivitas,
            'oleh'         => $oleh,
        ]);
    }

    public function getByPengaduan(int $pengaduanId): array
    {
        return $this->where('pengaduan_id', $pengaduanId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
