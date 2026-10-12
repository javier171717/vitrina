<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessModel extends Model
{
    protected $table         = 'businesses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'name', 'slug', 'tagline', 'phone', 'email', 'address', 'city',
        'logo', 'banner', 'banner_mobile', 'primary_color', 'instagram', 'facebook',
        'tiktok', 'show_prices', 'wholesale_min_total', 'is_active',
        'plan', 'plan_label', 'trial_ends_at', 'next_billing_at', 'nit', 'suspended',
    ];

    public function findByUserId(int $userId): ?array
    {
        $row = $this->where('user_id', $userId)->first();

        return $row ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $row = $this->where('slug', $slug)->first();

        return $row ?: null;
    }

    public function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = slugify($name);
        if (is_reserved_slug($base)) {
            $base .= '-negocio';
        }

        $slug = $base;
        $i    = 2;

        while ($this->slugTaken($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function slugTaken(string $slug, ?int $ignoreId = null): bool
    {
        $builder = $this->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function forAdminList(): array
    {
        return $this->db->table('businesses b')
            ->select('b.*, u.name AS owner_name, u.email AS owner_email, u.is_active AS user_is_active')
            ->join('users u', 'u.id = b.user_id')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
