<?php

namespace App\Controllers\Panel;

use App\Libraries\Uploader;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use RuntimeException;

class Products extends BasePanelController
{
    public function index(): string
    {
        return $this->panelView('panel/productos/index', [
            'title'    => 'Productos',
            'section'  => 'productos',
            'products' => model(ProductModel::class)->forBusiness((int) $this->business['id']),
        ]);
    }

    public function create(): string
    {
        return $this->panelView('panel/productos/form', [
            'title'      => 'Nuevo producto',
            'section'    => 'productos',
            'product'    => null,
            'categories' => model(CategoryModel::class)->forBusiness((int) $this->business['id']),
        ]);
    }

    public function store()
    {
        return $this->persist();
    }

    public function edit(int $id): string
    {
        $product = model(ProductModel::class)->findOwned($id, (int) $this->business['id']);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->panelView('panel/productos/form', [
            'title'      => 'Editar producto',
            'section'    => 'productos',
            'product'    => $product,
            'categories' => model(CategoryModel::class)->forBusiness((int) $this->business['id']),
        ]);
    }

    public function update(int $id)
    {
        return $this->persist($id);
    }

    public function delete(int $id)
    {
        $model   = model(ProductModel::class);
        $product = $model->findOwned($id, (int) $this->business['id']);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        (new Uploader())->delete($product['image'] ?? null);
        $model->delete($id);

        return redirect()->to(site_url('panel/productos'))->with('success', 'Producto eliminado.');
    }

    private function persist(?int $id = null)
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[190]',
            'sku'         => 'permit_empty|max_length[80]',
            'compatible'  => 'permit_empty|max_length[255]',
            'description' => 'permit_empty',
            'colors'      => 'permit_empty',
            'sizes'       => 'permit_empty',
            'price'           => 'permit_empty',
            'wholesale_price' => 'permit_empty',
            'category_id' => 'permit_empty|is_natural',
            'image'       => 'permit_empty|is_image[image]|max_size[image,4096]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model      = model(ProductModel::class);
        $categoryId = (int) $this->request->getPost('category_id');
        if ($categoryId > 0) {
            $category = model(CategoryModel::class)->findOwned($categoryId, (int) $this->business['id']);
            if (! $category) {
                $categoryId = 0;
            }
        }

        $price          = parse_price_cop($this->request->getPost('price'));
        $wholesalePrice = parse_price_cop($this->request->getPost('wholesale_price'));

        if (trim((string) $this->request->getPost('price')) !== '' && $price === null) {
            return redirect()->back()->withInput()->with('error', 'El precio al detal no es válido. Usa un valor como 20.000');
        }
        if (trim((string) $this->request->getPost('wholesale_price')) !== '' && $wholesalePrice === null) {
            return redirect()->back()->withInput()->with('error', 'El precio al por mayor no es válido. Usa un valor como 20.000');
        }

        $colors = parse_product_colors($this->request->getPost('colors'));
        $sizes  = parse_product_colors($this->request->getPost('sizes'));
        $data           = [
            'business_id'     => (int) $this->business['id'],
            'category_id'     => $categoryId > 0 ? $categoryId : null,
            'name'            => trim((string) $this->request->getPost('name')),
            'sku'             => trim((string) $this->request->getPost('sku')) ?: null,
            'compatible'      => trim((string) $this->request->getPost('compatible')) ?: null,
            'colors'          => $colors !== [] ? json_encode($colors, JSON_UNESCAPED_UNICODE) : null,
            'sizes'           => $sizes !== [] ? json_encode($sizes, JSON_UNESCAPED_UNICODE) : null,
            'description'     => trim((string) $this->request->getPost('description')) ?: null,
            'price'           => $price,
            'wholesale_price' => $wholesalePrice,
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id === null) {
            $data['is_active'] = $this->request->getPost('is_active') === null ? 1 : ($this->request->getPost('is_active') ? 1 : 0);
        }

        try {
            $stored = (new Uploader())->store($this->request->getFile('image'), (int) $this->business['id'], 'products');
            if ($stored) {
                if ($id) {
                    $current = $model->findOwned($id, (int) $this->business['id']);
                    (new Uploader())->delete($current['image'] ?? null);
                }
                $data['image'] = $stored;
            }
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        if ($id) {
            $product = $model->findOwned($id, (int) $this->business['id']);
            if (! $product) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            $model->update($id, $data);

            return redirect()->to(site_url('panel/productos'))->with('success', 'Producto actualizado.');
        }

        $model->insert($data);

        return redirect()->to(site_url('panel/productos'))->with('success', 'Producto publicado en tu catálogo.');
    }
}
