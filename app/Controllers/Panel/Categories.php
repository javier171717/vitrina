<?php

namespace App\Controllers\Panel;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Categories extends BasePanelController
{
    public function index(): string
    {
        return $this->panelView('panel/categorias/index', [
            'title'      => 'Categorías',
            'section'    => 'categorias',
            'categories' => model(CategoryModel::class)->forBusiness((int) $this->business['id']),
        ]);
    }

    public function store()
    {
        $ajax = $this->request->isAJAX()
            || str_contains(strtolower($this->request->getHeaderLine('Accept')), 'application/json');

        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            try {
                $decoded = $this->request->getJSON(true);
                $name    = trim((string) ($decoded['name'] ?? ''));
            } catch (\Throwable $e) {
                $name = '';
            }
        }

        if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
            if ($ajax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok'    => false,
                    'error' => 'Escribe un nombre de categoría (mínimo 2 caracteres).',
                    'csrf'  => csrf_hash(),
                ]);
            }

            return redirect()->back()->withInput()->with('errors', ['name' => 'Escribe un nombre de categoría (mínimo 2 caracteres).']);
        }

        $model      = model(CategoryModel::class);
        $businessId = (int) $this->business['id'];
        $existing   = $model->where('business_id', $businessId)->where('name', $name)->first();

        if ($existing) {
            if ($ajax) {
                return $this->response->setJSON([
                    'ok'   => true,
                    'id'   => (int) $existing['id'],
                    'name' => $existing['name'],
                    'csrf' => csrf_hash(),
                ]);
            }

            return redirect()->to(site_url('panel/categorias'))->with('success', 'Esa categoría ya existe.');
        }

        $model->insert([
            'business_id' => $businessId,
            'name'        => $name,
            'slug'        => $model->uniqueSlug($businessId, $name),
        ]);

        if ($ajax) {
            return $this->response->setJSON([
                'ok'   => true,
                'id'   => (int) $model->getInsertID(),
                'name' => $name,
                'csrf' => csrf_hash(),
            ]);
        }

        return redirect()->to(site_url('panel/categorias'))->with('success', 'Categoría creada.');
    }

    public function update(int $id)
    {
        $model    = model(CategoryModel::class);
        $category = $model->findOwned($id, (int) $this->business['id']);
        if (! $category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = ['name' => 'required|min_length[2]|max_length[120]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim((string) $this->request->getPost('name'));
        $model->update($id, [
            'name' => $name,
            'slug' => $model->uniqueSlug((int) $this->business['id'], $name, $id),
        ]);

        return redirect()->to(site_url('panel/categorias'))->with('success', 'Categoría actualizada.');
    }

    public function delete(int $id)
    {
        $model    = model(CategoryModel::class);
        $category = $model->findOwned($id, (int) $this->business['id']);
        if (! $category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        model(ProductModel::class)
            ->where('business_id', (int) $this->business['id'])
            ->where('category_id', $id)
            ->set(['category_id' => null])
            ->update();

        $model->delete($id);

        return redirect()->to(site_url('panel/categorias'))->with('success', 'Categoría eliminada.');
    }
}
