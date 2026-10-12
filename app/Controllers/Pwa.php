<?php

namespace App\Controllers;

class Pwa extends BaseController
{
    public function manifest()
    {
        $basePath = parse_url(base_url(), PHP_URL_PATH) ?: '/';
        if (! str_ends_with($basePath, '/')) {
            $basePath .= '/';
        }

        $name  = trim((string) $this->request->getGet('name'));
        $name  = $name !== '' ? mb_substr($name, 0, 60) : 'Vitrina';
        $color = (string) $this->request->getGet('color');
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#e30613';
        }

        $start = (string) $this->request->getGet('start');
        if (
            $start === ''
            || str_contains($start, '//')
            || ! str_starts_with($start, rtrim($basePath, '/') ?: '/')
            || ! preg_match('#^/[\w.\-/]*$#', $start)
        ) {
            $start = $basePath;
        }

        $short = mb_strlen($name) > 12 ? mb_substr($name, 0, 12) : $name;
        $icon192 = base_url('assets/img/icon-192.png');
        $icon512 = base_url('assets/img/icon-512.png');

        $payload = [
            'id'               => $start,
            'name'             => $name,
            'short_name'       => $short,
            'description'      => 'Catálogo digital para cotizar y vender.',
            'start_url'        => $start,
            'scope'            => $basePath,
            'display'          => 'standalone',
            'orientation'      => 'any',
            'background_color' => '#f4f1ec',
            'theme_color'      => $color,
            'lang'             => 'es',
            'icons'            => [
                [
                    'src'     => $icon192,
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src'     => $icon512,
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ];

        return $this->response
            ->setHeader('Cache-Control', 'no-cache')
            ->setContentType('application/manifest+json; charset=UTF-8')
            ->setBody(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
