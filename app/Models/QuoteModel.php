<?php

namespace App\Models;

use CodeIgniter\Model;

class QuoteModel extends Model
{
    protected $table         = 'quotes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['uuid', 'business_id', 'advisor_id', 'items', 'created_at'];

    public function findByUuid(string $uuid): ?array
    {
        $row = $this->where('uuid', $uuid)->first();

        return $row ?: null;
    }
}
