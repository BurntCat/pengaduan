<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table            = 'lokasi_kejadian';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_lokasi', 'kode_wilayah', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getActive()
    {
        return $this->where('is_active', 1)->orderBy('nama_lokasi', 'ASC')->findAll();
    }
}
