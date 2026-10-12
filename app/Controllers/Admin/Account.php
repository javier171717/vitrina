<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Account extends BaseAdminController
{
    public function index(): string
    {
        return $this->adminView('admin/cuenta', [
            'title'   => 'Tu cuenta',
            'section' => 'cuenta',
        ]);
    }

    public function save()
    {
        $userId = (int) $this->user['id'];
        $rules  = [
            'name'             => 'required|min_length[2]|max_length[120]',
            'email'            => "required|valid_email|max_length[190]|is_unique[users.email,id,{$userId}]",
            'current_password' => 'permit_empty',
            'new_password'     => 'permit_empty|min_length[8]',
        ];
        $messages = [
            'name' => [
                'required'   => 'Escribe el nombre.',
                'min_length' => 'El nombre debe tener al menos 2 caracteres.',
            ],
            'email' => [
                'required'    => 'Escribe el correo de acceso.',
                'valid_email' => 'Ese correo no es válido.',
                'is_unique'   => 'Ese correo ya está en uso. Elige otro.',
            ],
            'new_password' => [
                'min_length' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name         = trim((string) $this->request->getPost('name'));
        $email        = strtolower(trim((string) $this->request->getPost('email')));
        $current      = (string) $this->request->getPost('current_password');
        $new          = (string) $this->request->getPost('new_password');
        $emailChanged = $email !== strtolower((string) $this->user['email']);

        if ($emailChanged || $new !== '') {
            if ($current === '' || ! password_verify($current, $this->user['password_hash'])) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'Para cambiar el correo o la contraseña debes escribir la contraseña actual. No se guardaron los cambios.'
                );
            }
        }

        $data = [
            'name'  => $name,
            'email' => $email,
        ];

        if ($new !== '') {
            if (password_verify($new, $this->user['password_hash'])) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'La nueva contraseña debe ser distinta a la actual. No se guardaron los cambios.'
                );
            }
            $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
        }

        model(UserModel::class)->update($userId, $data);
        session()->set('user_name', $data['name']);

        $ok = 'Datos actualizados.';
        if ($emailChanged) {
            $ok = 'Correo de acceso actualizado. Úsalo la próxima vez que ingreses.';
        } elseif ($new !== '') {
            $ok = 'Contraseña actualizada. Úsala la próxima vez que ingreses.';
        }

        return redirect()->to(site_url('admin/cuenta'))->with('success', $ok);
    }
}
