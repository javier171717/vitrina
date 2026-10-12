<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseAdminController extends BaseController
{
    /** @var array<string, mixed> */
    protected array $user = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $userId = (int) session()->get('user_id');
        $user   = model(UserModel::class)->find($userId);

        if (! $user || ($user['role'] ?? '') !== 'admin') {
            session()->destroy();
            redirect()->to(site_url('ingresar'))->with('error', 'Inicia sesión de nuevo.')->send();
            exit;
        }

        $this->user = $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function adminView(string $view, array $data = []): string
    {
        return view($view, array_merge([
            'user'    => $this->user,
            'section' => $data['section'] ?? '',
        ], $data));
    }
}
