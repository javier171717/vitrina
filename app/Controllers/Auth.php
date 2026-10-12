<?php

namespace App\Controllers;

use App\Models\BusinessModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login(): string
    {
        return view('auth/login', [
            'title' => 'Ingresar',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower((string) $this->request->getPost('email'));
        $user  = model(UserModel::class)->where('email', $email)->first();

        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Correo o contraseña incorrectos.');
        }

        if ((int) ($user['is_active'] ?? 1) !== 1) {
            return redirect()->back()->withInput()->with('error', 'Tu cuenta está desactivada. Habla con el administrador del sistema.');
        }

        session()->regenerate();
        session()->remove('plan_notice_shown');
        session()->set([
            'user_id'    => (int) $user['id'],
            'user_name'  => $user['name'],
            'user_role'  => $user['role'] ?? 'owner',
        ]);

        if (($user['role'] ?? '') === 'admin') {
            return redirect()->to(site_url('admin'))->with('success', 'Bienvenido de nuevo.');
        }

        return redirect()->to(site_url('panel'))->with('success', 'Bienvenido de nuevo.');
    }

    public function register(): string
    {
        return view('auth/register', [
            'title' => 'Crear mi catálogo',
        ]);
    }

    public function attemptRegister()
    {
        if (! registration_is_open()) {
            return redirect()->to(site_url('registro'))->with('error', 'El registro está cerrado. Pide acceso al administrador.');
        }
        $rules = [
            'name'          => 'required|min_length[2]|max_length[120]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[8]',
            'business_name' => 'required|min_length[2]|max_length[160]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $users      = model(UserModel::class);
        $businesses = model(BusinessModel::class);
        $db         = db_connect();
        $db->transStart();

        $userId = $users->insert([
            'name'          => trim((string) $this->request->getPost('name')),
            'email'         => strtolower(trim((string) $this->request->getPost('email'))),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'owner',
            'is_active'     => 1,
        ]);
        $businessName = trim((string) $this->request->getPost('business_name'));
        $businesses->insert([
            'user_id'       => $userId,
            'name'          => $businessName,
            'slug'          => $businesses->uniqueSlug($businessName),
            'primary_color' => '#e30613',
            'show_prices'   => 1,
            'is_active'     => 1,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'No se pudo crear la cuenta. Inténtalo de nuevo.');
        }

        session()->regenerate();
        session()->set([
            'user_id'   => (int) $userId,
            'user_name' => trim((string) $this->request->getPost('name')),
            'user_role' => 'owner',
        ]);

        return redirect()->to(site_url('panel'))->with('success', 'Tu catálogo ya está creado. Ahora agrega un asesor y tus productos.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('/'));
    }
}
