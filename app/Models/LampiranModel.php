<?php

namespace App\Models;

use CodeIgniter\Model;

class LampiranModel extends Model
{
    protected $table         = 'pengaduan_lampiran';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['pengaduan_id', 'nama_file', 'path_file', 'mime_type', 'ukuran_kb'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getByPengaduan(int $pengaduanId): array
    {
        return $this->where('pengaduan_id', $pengaduanId)->findAll();
    }
}
