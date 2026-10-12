<?php

namespace App\Controllers\Panel;

use App\Models\AdvisorModel;

class Advisors extends BasePanelController
{
    public function index(): string
    {
        return $this->panelView('panel/asesores/index', [
            'title'    => 'Asesores',
            'section'  => 'asesores',
            'advisors' => model(AdvisorModel::class)->forBusiness((int) $this->business['id']),
        ]);
    }

    public function create(): string
    {
        return $this->panelView('panel/asesores/form', [
            'title'   => 'Nuevo asesor',
            'section' => 'asesores',
            'advisor' => null,
        ]);
    }

    public function store()
    {
        return $this->persist();
    }

    public function edit(int $id): string
    {
        $advisor = model(AdvisorModel::class)->findOwned($id, (int) $this->business['id']);
        if (! $advisor) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->panelView('panel/asesores/form', [
            'title'   => 'Editar asesor',
            'section' => 'asesores',
            'advisor' => $advisor,
        ]);
    }

    public function update(int $id)
    {
        return $this->persist($id);
    }

    public function delete(int $id)
    {
        $model   = model(AdvisorModel::class);
        $advisor = $model->findOwned($id, (int) $this->business['id']);
        if (! $advisor) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $model->delete($id);

        return redirect()->to(site_url('panel/asesores'))->with('success', 'Asesor eliminado.');
    }

    private function persist(?int $id = null)
    {
        $rules = [
            'name'      => 'required|min_length[2]|max_length[120]',
            'phone'     => 'required|min_length[8]|max_length[40]',
            'coverage'  => 'permit_empty|max_length[255]',
            'slug'      => 'permit_empty|max_length[120]|alpha_dash',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model    = model(AdvisorModel::class);
        $name     = trim((string) $this->request->getPost('name'));
        $slugPost = trim((string) $this->request->getPost('slug'));
        $slug     = $slugPost !== '' ? slugify($slugPost) : $model->uniqueSlug((int) $this->business['id'], $name, $id);

        if ($model->slugTaken((int) $this->business['id'], $slug, $id)) {
            return redirect()->back()->withInput()->with('error', 'Ese enlace de asesor ya existe.');
        }

        $payload = [
            'business_id' => (int) $this->business['id'],
            'name'        => $name,
            'slug'        => $slug,
            'phone'       => trim((string) $this->request->getPost('phone')),
            'coverage'    => trim((string) $this->request->getPost('coverage')) ?: null,
        ];

        if ($id) {
            $advisor = $model->findOwned($id, (int) $this->business['id']);
            if (! $advisor) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            $model->update($id, $payload);
            $msg = 'Asesor actualizado.';
        } else {
            $model->insert($payload);
            $msg = 'Asesor creado. Ya puedes compartir su catálogo.';
        }

        return redirect()->to(site_url('panel/asesores'))->with('success', $msg);
    }
}
