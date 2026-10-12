<?php

namespace App\Controllers\Panel;

use App\Libraries\Uploader;
use App\Models\BusinessModel;
use App\Models\UserModel;
use RuntimeException;

class BusinessSettings extends BasePanelController
{
    public function index(): string
    {
        return $this->panelView('panel/negocio', [
            'title'   => 'Mi negocio',
            'section' => 'negocio',
        ]);
    }

    public function save()
    {
        $rules = [
            'name'          => 'required|min_length[2]|max_length[160]',
            'slug'          => 'required|min_length[2]|max_length[160]|alpha_dash',
            'tagline'       => 'permit_empty|max_length[255]',
            'phone'         => 'permit_empty|max_length[40]',
            'email'         => 'permit_empty|valid_email|max_length[190]',
            'address'       => 'permit_empty|max_length[255]',
            'city'          => 'permit_empty|max_length[120]',
            'primary_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'instagram'     => 'permit_empty|max_length[255]',
            'facebook'      => 'permit_empty|max_length[255]',
            'tiktok'        => 'permit_empty|max_length[255]',
            'logo'          => 'permit_empty|is_image[logo]|max_size[logo,4096]',
            'banner'        => 'permit_empty|is_image[banner]|max_size[banner,6144]',
            'banner_mobile' => 'permit_empty|is_image[banner_mobile]|max_size[banner_mobile,4096]',
        ];

        $removeField = (string) $this->request->getPost('remove_image');
        if (in_array($removeField, ['logo', 'banner', 'banner_mobile'], true)) {
            $this->clearImage($removeField);

            $labels = [
                'logo'          => 'Logo eliminado.',
                'banner'        => 'Banner de escritorio eliminado.',
                'banner_mobile' => 'Banner de celular eliminado.',
            ];

            return redirect()->to(site_url('panel/negocio'))->with('success', $labels[$removeField]);
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $businesses = model(BusinessModel::class);
        $slug       = slugify((string) $this->request->getPost('slug'));

        if (is_reserved_slug($slug)) {
            return redirect()->back()->withInput()->with('error', 'Esa URL está reservada. Elige otra.');
        }

        if ($businesses->slugTaken($slug, (int) $this->business['id'])) {
            return redirect()->back()->withInput()->with('error', 'Esa URL ya la usa otro negocio.');
        }

        $uploader = new Uploader();
        $data     = [
            'name'          => trim((string) $this->request->getPost('name')),
            'slug'          => $slug,
            'tagline'       => trim((string) $this->request->getPost('tagline')) ?: null,
            'phone'         => trim((string) $this->request->getPost('phone')) ?: null,
            'email'         => trim((string) $this->request->getPost('email')) ?: null,
            'address'       => trim((string) $this->request->getPost('address')) ?: null,
            'city'          => trim((string) $this->request->getPost('city')) ?: null,
            'primary_color' => $this->request->getPost('primary_color') ?: '#e30613',
            'instagram'     => trim((string) $this->request->getPost('instagram')) ?: null,
            'facebook'      => trim((string) $this->request->getPost('facebook')) ?: null,
            'tiktok'        => trim((string) $this->request->getPost('tiktok')) ?: null,
            'show_prices'   => $this->request->getPost('show_prices') ? 1 : 0,
        ];

        $wholesaleMinRaw = trim((string) $this->request->getPost('wholesale_min_total'));
        if ($wholesaleMinRaw !== '') {
            $wholesaleMinTotal = parse_price_cop($wholesaleMinRaw);
            if ($wholesaleMinTotal === null || $wholesaleMinTotal < 1) {
                return redirect()->back()->withInput()->with('error', 'El monto para precio al por mayor no es válido. Usa un valor como 301.000.');
            }
            $data['wholesale_min_total'] = $wholesaleMinTotal;
        } else {
            $data['wholesale_min_total'] = null;
        }

        try {
            foreach (['logo' => 'logo', 'banner' => 'banner', 'banner_mobile' => 'banner'] as $field => $folder) {
                $file = $this->request->getFile($field);
                if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE && ! $file->isValid()) {
                    throw new RuntimeException('No se pudo subir el archivo: ' . $file->getErrorString());
                }

                $stored = $uploader->store($file, (int) $this->business['id'], $folder);
                if ($stored) {
                    $uploader->delete($this->business[$field] ?? null);
                    $data[$field] = $stored;
                }
            }
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $businesses->update((int) $this->business['id'], $data);

        return redirect()->to(site_url('panel/negocio'))->with('success', 'Datos del negocio actualizados.');
    }

    public function changePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];
        $messages = [
            'current_password' => [
                'required' => 'Escribe tu contraseña actual para confirmar que eres tú.',
            ],
            'new_password' => [
                'required'   => 'Escribe la nueva contraseña.',
                'min_length' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            ],
            'confirm_password' => [
                'required' => 'Confirma la nueva contraseña.',
                'matches'  => 'La confirmación no coincide con la nueva contraseña.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to(site_url('panel/negocio') . '#password')
                ->withInput()
                ->with('password_errors', array_values($this->validator->getErrors()));
        }

        $current = (string) $this->request->getPost('current_password');
        $new     = (string) $this->request->getPost('new_password');

        if (! password_verify($current, $this->user['password_hash'])) {
            return redirect()->to(site_url('panel/negocio') . '#password')
                ->withInput()
                ->with('password_error', 'No se cambió la contraseña porque la actual no es correcta. Revísala e inténtalo de nuevo.');
        }

        if (password_verify($new, $this->user['password_hash'])) {
            return redirect()->to(site_url('panel/negocio') . '#password')
                ->with('password_error', 'No se cambió la contraseña porque la nueva es igual a la actual. Elige una distinta.');
        }

        model(UserModel::class)->update((int) $this->user['id'], [
            'password_hash' => password_hash($new, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(site_url('panel/negocio') . '#password')
            ->with('password_success', 'Contraseña actualizada. Úsala en el próximo inicio de sesión.');
    }

    private function clearImage(string $field): void
    {
        $current = $this->business[$field] ?? null;
        (new Uploader())->delete($current);

        model(BusinessModel::class)->update((int) $this->business['id'], [
            $field => null,
        ]);
    }
}
