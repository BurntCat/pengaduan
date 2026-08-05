<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori_pengaduan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_kategori', 'is_active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getActive()
    {
        return $this->where('is_active', 1)->orderBy('nama_kategori', 'ASC')->findAll();
    }
}
