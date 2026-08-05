<?php

namespace App\Models;

use CodeIgniter\Model;

class SlaDefaultModel extends Model
{
    protected $table         = 'sla_default';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['kategori_id', 'tahap', 'sla_hari', 'urutan'];

    /**
     * Ambil SLA default untuk satu kategori, urut berdasarkan tahap.
     */
    public function getByKategori(int $kategoriId): array
    {
        return $this->where('kategori_id', $kategoriId)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    public function updateSla(int $id, int $slaHari): bool
    {
        return $this->update($id, ['sla_hari' => $slaHari]);
    }
}
