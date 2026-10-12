<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'business_id', 'category_id', 'name', 'sku', 'description',
        'compatible', 'colors', 'sizes', 'price', 'wholesale_price', 'wholesale_qty', 'image', 'is_active',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public function forBusiness(int $businessId, bool $activeOnly = false): array
    {
        $builder = $this->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.business_id', $businessId);

        if ($activeOnly) {
            $builder->where('products.is_active', 1);
        }

        return $builder->orderBy('products.name', 'ASC')->findAll();
    }

    public function findOwned(int $id, int $businessId): ?array
    {
        $row = $this->where('id', $id)->where('business_id', $businessId)->first();

        return $row ?: null;
    }

    public function countForBusiness(int $businessId): int
    {
        return $this->where('business_id', $businessId)->countAllResults();
    }
}
