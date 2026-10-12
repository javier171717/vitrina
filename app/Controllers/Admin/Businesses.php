<?php

namespace App\Controllers\Admin;

use App\Models\BusinessModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Businesses extends BaseAdminController
{
    public function index(): string
    {
        return $this->adminView('admin/negocios', [
            'title'      => 'Negocios',
            'section'    => 'negocios',
            'businesses' => model(BusinessModel::class)->forAdminList(),
        ]);
    }

    public function createForm(): string
    {
        return $this->adminView('admin/negocio_alta', [
            'title'   => 'Alta de negocio y dueño',
            'section' => 'negocios',
        ]);
    }

    public function store()
    {
        $rules = [
            'owner_name'    => 'required|min_length[2]|max_length[120]',
            'owner_email'   => 'required|valid_email|is_unique[users.email]',
            'owner_password'=> 'required|min_length[8]',
            'business_name' => 'required|min_length[2]|max_length[160]',
            'nit'           => 'permit_empty|max_length[60]',
            'plan'          => 'required|in_list[trial,active]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $users      = model(UserModel::class);
        $businesses = model(BusinessModel::class);
        $db         = db_connect();
        $db->transStart();

        $userId = $users->insert([
            'name'          => trim((string) $this->request->getPost('owner_name')),
            'email'         => strtolower(trim((string) $this->request->getPost('owner_email'))),
            'password_hash' => password_hash((string) $this->request->getPost('owner_password'), PASSWORD_DEFAULT),
            'role'          => 'owner',
            'is_active'     => 1,
        ]);

        $businessName = trim((string) $this->request->getPost('business_name'));
        $plan         = (string) $this->request->getPost('plan');
        $trialDays    = (int) ($this->request->getPost('trial_days') ?: 14);

        $data = [
            'user_id'       => $userId,
            'name'          => $businessName,
            'slug'          => $businesses->uniqueSlug($businessName),
            'primary_color' => '#e30613',
            'show_prices'   => 1,
            'is_active'     => 1,
            'plan'          => $plan,
            'plan_label'    => trim((string) $this->request->getPost('plan_label')) ?: null,
            'nit'           => trim((string) $this->request->getPost('nit')) ?: null,
            'suspended'     => 0,
        ];

        if ($plan === 'trial') {
            $data['trial_ends_at'] = date('Y-m-d H:i:s', strtotime("+{$trialDays} days"));
        } else {
            $billingRaw = trim((string) $this->request->getPost('next_billing_at'));
            $data['next_billing_at'] = $billingRaw !== '' ? $billingRaw : null;
        }

        $businesses->insert($data);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'No se pudo crear el negocio.');
        }

        return redirect()->to(site_url('admin/negocios'))->with('success', "Negocio «{$businessName}» creado. El dueño ya puede ingresar.");
    }

    public function detail(int $id): string
    {
        $business = model(BusinessModel::class)->find($id);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $owner = model(UserModel::class)->find((int) $business['user_id']);

        return $this->adminView('admin/negocio_detalle', [
            'title'    => $business['name'],
            'section'  => 'negocios',
            'business' => $business,
            'owner'    => $owner,
        ]);
    }

    public function savePlan(int $id)
    {
        $business = model(BusinessModel::class)->find($id);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $plan = (string) $this->request->getPost('plan');
        if (! in_array($plan, ['trial', 'active'], true)) {
            $plan = 'trial';
        }

        $data = [
            'plan'       => $plan,
            'plan_label' => trim((string) $this->request->getPost('plan_label')) ?: null,
        ];

        if ($plan === 'trial') {
            $trialRaw = trim((string) $this->request->getPost('trial_ends_at'));
            $data['trial_ends_at']   = $trialRaw !== '' ? $trialRaw : null;
            $data['next_billing_at'] = null;
        } else {
            $billingRaw = trim((string) $this->request->getPost('next_billing_at'));
            $data['trial_ends_at']   = null;
            $data['next_billing_at'] = $billingRaw !== '' ? $billingRaw : null;
        }

        model(BusinessModel::class)->update($id, $data);

        return redirect()->to(site_url('admin/negocios/' . $id))->with('success', 'Plan y fechas actualizados.');
    }

    public function saveInfo(int $id)
    {
        $business = model(BusinessModel::class)->find($id);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[160]',
            'nit'  => 'permit_empty|max_length[60]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        model(BusinessModel::class)->update($id, [
            'name' => trim((string) $this->request->getPost('name')),
            'nit'  => trim((string) $this->request->getPost('nit')) ?: null,
        ]);

        return redirect()->to(site_url('admin/negocios/' . $id))->with('success', 'Datos del negocio actualizados.');
    }

    public function resetPassword(int $id)
    {
        $business = model(BusinessModel::class)->find($id);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $newPass = (string) $this->request->getPost('new_password');
        if (strlen($newPass) < 8) {
            return redirect()->back()->with('error', 'La contraseña debe tener al menos 8 caracteres.');
        }

        model(UserModel::class)->update((int) $business['user_id'], [
            'password_hash' => password_hash($newPass, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(site_url('admin/negocios/' . $id))->with('success', 'Contraseña del dueño actualizada.');
    }

    public function toggleSuspend(int $id)
    {
        $business = model(BusinessModel::class)->find($id);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $next = (int) ($business['suspended'] ?? 0) === 1 ? 0 : 1;
        model(BusinessModel::class)->update($id, ['suspended' => $next]);

        $label = $next === 1 ? 'Negocio suspendido.' : 'Negocio reactivado.';

        return redirect()->to(site_url('admin/negocios/' . $id))->with('success', $label);
    }

    public function deleteBusiness(int $id)
    {
        $business = model(BusinessModel::class)->find($id);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $userId = (int) $business['user_id'];

        model(BusinessModel::class)->delete($id);
        model(UserModel::class)->delete($userId);

        return redirect()->to(site_url('admin/negocios'))->with('success', $business['name'] . ' eliminado completamente.');
    }
}
