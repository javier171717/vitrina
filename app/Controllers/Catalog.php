<?php

namespace App\Controllers;

use App\Models\AdvisorModel;
use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Catalog extends BaseController
{
    public function show(string $businessSlug)
    {
        $business = model(BusinessModel::class)->findBySlug($businessSlug);
        if (! $business || ! business_is_public($business)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $advisors = model(AdvisorModel::class)->forBusiness((int) $business['id']);
        if (count($advisors) === 1) {
            return redirect()->to(catalog_url($business['slug'], $advisors[0]['slug']));
        }

        return view('catalog/pick_advisor', [
            'title'    => $business['name'] . ' · Elige asesor',
            'business' => $business,
            'advisors' => $advisors,
        ]);
    }

    public function advisor(string $businessSlug, string $advisorSlug): string
    {
        $business = model(BusinessModel::class)->findBySlug($businessSlug);
        if (! $business || ! business_is_public($business)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $advisor = model(AdvisorModel::class)->findBySlug((int) $business['id'], $advisorSlug);
        if (! $advisor) {
            throw PageNotFoundException::forPageNotFound();
        }

        $categories = model(CategoryModel::class)->forBusiness((int) $business['id']);
        $products   = model(ProductModel::class)->forBusiness((int) $business['id'], true);

        $catalog = [];
        foreach ($products as $product) {
            $catalog[] = [
                'id'            => (int) $product['id'],
                'name'          => $product['name'],
                'sku'           => $product['sku'],
                'description'   => $product['description'],
                'compatible'    => $product['compatible'],
                'colors'        => parse_product_colors($product['colors'] ?? null),
                'sizes'         => parse_product_colors($product['sizes'] ?? null),
                'price'          => $product['price'] !== null ? (float) $product['price'] : null,
                'wholesale_price'=> $product['wholesale_price'] !== null ? (float) $product['wholesale_price'] : null,
                'image'         => $product['image'] ? upload_url($product['image']) : '',
                'category_id'   => $product['category_id'] ? (int) $product['category_id'] : 0,
                'category_name' => $product['category_name'] ?? '',
                'category_slug' => $product['category_slug'] ?? '',
            ];
        }

        return view('catalog/index', [
            'title'      => $business['name'] . ' · Cotiza con ' . $advisor['name'],
            'business'   => $business,
            'advisor'    => $advisor,
            'advisors'   => model(AdvisorModel::class)->forBusiness((int) $business['id']),
            'categories' => $categories,
            'catalog'    => $catalog,
        ]);
    }
}
