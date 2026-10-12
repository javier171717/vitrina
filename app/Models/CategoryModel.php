<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['business_id', 'name', 'slug', 'sort_order'];

    /**
     * @return list<array<string, mixed>>
     */
    public function forBusiness(int $businessId): array
    {
        return $this->where('business_id', $businessId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function findOwned(int $id, int $businessId): ?array
    {
        $row = $this->where('id', $id)->where('business_id', $businessId)->first();

        return $row ?: null;
    }

    public function uniqueSlug(int $businessId, string $name, ?int $ignoreId = null): string
    {
        $base = slugify($name);
        $slug = $base;
        $i    = 2;

        while ($this->slugTaken($businessId, $slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function slugTaken(int $businessId, string $slug, ?int $ignoreId = null): bool
    {
        $builder = $this->where('business_id', $businessId)->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }
}
