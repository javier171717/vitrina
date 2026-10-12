<?php

namespace App\Controllers\Admin;

use App\Models\BusinessModel;
use App\Models\SettingModel;
use App\Models\UserModel;

class Dashboard extends BaseAdminController
{
    public function index(): string
    {
        $businesses = model(BusinessModel::class);
        $users      = model(UserModel::class);

        return $this->adminView('admin/dashboard', [
            'title'             => 'Control',
            'section'           => 'dashboard',
            'registrationOpen'  => registration_is_open(),
            'businessCount'     => $businesses->countAllResults(),
            'activeCatalogs'    => $businesses->where('is_active', 1)->countAllResults(),
            'ownerCount'        => $users->where('role', 'owner')->countAllResults(),
        ]);
    }

    public function saveRegistration()
    {
        $open = $this->request->getPost('registration_open') ? '1' : '0';
        model(SettingModel::class)->setValue('registration_open', $open);

        $message = $open === '1'
            ? 'El registro está abierto. Los clientes ya pueden crear su catálogo.'
            : 'El registro quedó cerrado. Nadie más puede crear un catálogo por su cuenta.';

        return redirect()->to(site_url('admin'))->with('success', $message);
    }
}
