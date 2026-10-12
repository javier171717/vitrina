<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table         = 'settings';
    protected $primaryKey    = 'key';
    protected $useAutoIncrement = false;
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['key', 'value'];

    public function getValue(string $key, string $default = ''): string
    {
        $row = $this->db->table($this->table)->where('key', $key)->get()->getRowArray();

        if (! $row || $row['value'] === null || $row['value'] === '') {
            return $default;
        }

        return (string) $row['value'];
    }

    public function setValue(string $key, string $value): void
    {
        $this->db->table($this->table)->replace([
            'key'   => $key,
            'value' => $value,
        ]);
    }
}
