<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return redirect()->to(site_url('ingresar'))
                ->with('error', 'Inicia sesión para entrar al panel de control.');
        }

        $user = model(UserModel::class)->find($userId);
        if (! $user || ($user['role'] ?? '') !== 'admin' || (int) ($user['is_active'] ?? 0) !== 1) {
            return redirect()->to(site_url('panel'))
                ->with('error', 'No tienes acceso al panel de control.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
