<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\BusinessModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BasePanelController extends BaseController
{
    /** @var array<string, mixed> */
    protected array $user = [];

    /** @var array<string, mixed> */
    protected array $business = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $userId = (int) session()->get('user_id');
        $user   = model(UserModel::class)->find($userId);

        if (! $user) {
            session()->destroy();
            redirect()->to(site_url('ingresar'))->with('error', 'Inicia sesión de nuevo.')->send();
            exit;
        }

        if (($user['role'] ?? '') === 'admin') {
            redirect()->to(site_url('admin'))->send();
            exit;
        }

        if ((int) ($user['is_active'] ?? 1) !== 1) {
            session()->destroy();
            redirect()->to(site_url('ingresar'))->with('error', 'Tu cuenta está desactivada.')->send();
            exit;
        }

        $business = model(BusinessModel::class)->findByUserId((int) $user['id']);
        if (! $business) {
            $businesses = model(BusinessModel::class);
            $id         = $businesses->insert([
                'user_id'       => $user['id'],
                'name'          => $user['name'],
                'slug'          => $businesses->uniqueSlug($user['name']),
                'primary_color' => '#e30613',
                'show_prices'   => 1,
                'is_active'     => 1,
            ]);
            $business = $businesses->find($id);
        }

        $this->user     = $user;
        $this->business = $business;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function panelView(string $view, array $data = []): string
    {
        return view($view, array_merge([
            'user'     => $this->user,
            'business' => $this->business,
            'section'  => $data['section'] ?? '',
        ], $data));
    }
}
