<?php

if (! function_exists('slugify')) {
    function slugify(string $text): string
    {
        $map = [
            'Á' => 'a', 'À' => 'a', 'Ä' => 'a', 'Â' => 'a', 'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a',
            'É' => 'e', 'È' => 'e', 'Ë' => 'e', 'Ê' => 'e', 'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'Í' => 'i', 'Ì' => 'i', 'Ï' => 'i', 'Î' => 'i', 'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'Ó' => 'o', 'Ò' => 'o', 'Ö' => 'o', 'Ô' => 'o', 'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'Ú' => 'u', 'Ù' => 'u', 'Ü' => 'u', 'Û' => 'u', 'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'Ñ' => 'n', 'ñ' => 'n', 'Ç' => 'c', 'ç' => 'c',
        ];

        $text = strtr($text, $map);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        $text = trim($text, '-');

        return $text !== '' ? $text : 'item';
    }
}

if (! function_exists('reserved_slugs')) {
    /**
     * @return list<string>
     */
    function reserved_slugs(): array
    {
        return [
            'panel', 'ingresar', 'registro', 'salir', 'c', 'admin', 'assets',
            'uploads', 'api', 'login', 'register', 'logout', 'catalogo', 'club',
            'terminos', 'privacidad', 'index', 'public',
        ];
    }
}

if (! function_exists('is_reserved_slug')) {
    function is_reserved_slug(string $slug): bool
    {
        return in_array(strtolower($slug), reserved_slugs(), true);
    }
}

if (! function_exists('whatsapp_digits')) {
    function whatsapp_digits(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 10 && str_starts_with($digits, '3')) {
            return '57' . $digits;
        }

        return $digits;
    }
}

if (! function_exists('money_cop')) {
    function money_cop(mixed $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return '$ ' . number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('format_price_cop')) {
    function format_price_cop(mixed $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('parse_price_cop')) {
    function parse_price_cop(mixed $value): ?int
    {
        $value = trim(str_replace(['$', ' '], '', (string) $value));
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',') && ! str_contains($value, '.')) {
            $value = str_replace(',', '.', $value);
        }

        if (! is_numeric($value)) {
            return null;
        }

        return (int) round((float) $value);
    }
}

if (! function_exists('nullable_price')) {
    function nullable_price(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}

if (! function_exists('cart_uses_wholesale_total')) {
    /**
     * True when the cart retail total reaches the business-wide wholesale threshold.
     *
     * @param list<array{product: array<string, mixed>, qty: int}> $lines
     */
    function cart_uses_wholesale_total(array $lines, mixed $minTotal): bool
    {
        $min = nullable_price($minTotal);
        if ($min === null || $min <= 0) {
            return false;
        }

        $retailTotal = 0.0;
        foreach ($lines as $line) {
            $product = $line['product'] ?? [];
            $qty     = (int) ($line['qty'] ?? 0);
            $retail  = nullable_price($product['price'] ?? null);
            if ($retail !== null && $qty > 0) {
                $retailTotal += $retail * $qty;
            }
        }

        return $retailTotal >= $min;
    }
}

if (! function_exists('product_unit_price')) {
    /**
     * @param array<string, mixed> $product
     */
    function product_unit_price(array $product, int $qty, bool $forceWholesale = false): ?float
    {
        $retail    = nullable_price($product['price'] ?? null);
        $wholesale = nullable_price($product['wholesale_price'] ?? null);

        if ($wholesale !== null && $forceWholesale) {
            return $wholesale;
        }

        return $retail;
    }
}

if (! function_exists('product_is_wholesale')) {
    /**
     * @param array<string, mixed> $product
     */
    function product_is_wholesale(array $product, int $qty, bool $forceWholesale = false): bool
    {
        $wholesale = nullable_price($product['wholesale_price'] ?? null);

        return $wholesale !== null && $forceWholesale;
    }
}

if (! function_exists('parse_product_colors')) {
    /**
     * @param mixed $value
     * @return list<string>
     */
    function parse_product_colors(mixed $value): array
    {
        if (is_string($value)) {
            $trim = trim($value);
            if ($trim === '') {
                return [];
            }
            $decoded = json_decode($trim, true);
            $value   = is_array($decoded) ? $decoded : (preg_split('/[,;|]+/', $trim) ?: []);
        }
        if (! is_array($value)) {
            return [];
        }

        $seen = [];
        $out  = [];
        foreach ($value as $item) {
            $name = trim((string) $item);
            if ($name === '') {
                continue;
            }
            $name = mb_substr($name, 0, 40);
            $key  = mb_strtolower($name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[]      = $name;
            if (count($out) >= 20) {
                break;
            }
        }

        return $out;
    }
}

if (! function_exists('product_color_hex')) {
    function product_color_hex(string $name): string
    {
        $key = mb_strtolower(trim($name));
        $map = [
            'negro' => '#171717', 'black' => '#171717',
            'dorado' => '#c9a227', 'oro' => '#c9a227', 'gold' => '#c9a227',
            'plateado' => '#6b7280', 'plata' => '#6b7280', 'silver' => '#6b7280', 'cromado' => '#94a3b8',
            'rojo' => '#dc2626', 'red' => '#dc2626',
            'azul' => '#2563eb', 'blue' => '#2563eb',
            'blanco' => '#9ca3af', 'white' => '#9ca3af',
            'verde' => '#16a34a', 'green' => '#16a34a',
            'naranja' => '#ea580c', 'orange' => '#ea580c',
            'gris' => '#6b7280', 'gray' => '#6b7280', 'grey' => '#6b7280',
            'rosa' => '#db2777', 'pink' => '#db2777',
            'amarillo' => '#ca8a04', 'yellow' => '#ca8a04',
            'morado' => '#7c3aed', 'violeta' => '#7c3aed', 'purple' => '#7c3aed',
            'cafe' => '#92400e', 'café' => '#92400e', 'marron' => '#92400e', 'marrón' => '#92400e', 'brown' => '#92400e',
        ];

        return $map[$key] ?? '#4b5563';
    }
}

if (! function_exists('upload_url')) {
    function upload_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        $generated = base_url($path);
        $relative  = parse_url($generated, PHP_URL_PATH);

        return $relative ?: '/' . ltrim($path, '/');
    }
}

if (! function_exists('favicon_tags')) {
    function favicon_tags(?array $business = null): string
    {
        $href = base_url('assets/img/favicon.svg');
        $type = 'image/svg+xml';

        if ($business && ! empty($business['logo'])) {
            $href = upload_url($business['logo']);
            $ext  = strtolower(pathinfo((string) $business['logo'], PATHINFO_EXTENSION));
            $type = match ($ext) {
                'svg'         => 'image/svg+xml',
                'png'         => 'image/png',
                'gif'         => 'image/gif',
                'webp'        => 'image/webp',
                'ico'         => 'image/x-icon',
                'jpg', 'jpeg' => 'image/jpeg',
                default       => 'image/png',
            };
        } elseif ($business) {
            $name   = trim((string) ($business['name'] ?? 'V'));
            $letter = strtoupper(substr($name !== '' ? $name : 'V', 0, 1));
            if (! preg_match('/[A-Z0-9]/', $letter)) {
                $letter = 'V';
            }
            $color = (string) ($business['primary_color'] ?? '');
            if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                $color = '#e30613';
            }
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
                . '<rect width="64" height="64" rx="14" fill="' . $color . '"/>'
                . '<text x="32" y="44" text-anchor="middle" font-family="Arial,sans-serif" font-size="36" font-weight="700" fill="#fff">' . $letter . '</text>'
                . '</svg>';
            $href = 'data:image/svg+xml,' . rawurlencode($svg);
        }

        $safe = esc($href, 'attr');

        $apple = $business && ! empty($business['logo'])
            ? $safe
            : esc(base_url('assets/img/apple-touch-icon.png'), 'attr');

        return '<link rel="icon" href="' . $safe . '" type="' . $type . '">' . "\n"
            . '  <link rel="apple-touch-icon" href="' . $apple . '">';
    }
}

if (! function_exists('pwa_tags')) {
    function pwa_tags(?array $business = null, ?string $themeColor = null): string
    {
        $name = trim((string) ($business['name'] ?? 'Vitrina'));
        if ($name === '') {
            $name = 'Vitrina';
        }

        $color = $themeColor ?? ($business['primary_color'] ?? '#e30613');
        if (! is_string($color) || ! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#e30613';
        }

        $start = parse_url((string) current_url(), PHP_URL_PATH) ?: '/';
        $manifest = site_url('manifest.webmanifest') . '?' . http_build_query([
            'name'  => $name,
            'color' => $color,
            'start' => $start,
        ]);

        return '<meta name="theme-color" content="' . esc($color, 'attr') . '">' . "\n"
            . '  <meta name="mobile-web-app-capable" content="yes">' . "\n"
            . '  <meta name="apple-mobile-web-app-capable" content="yes">' . "\n"
            . '  <meta name="apple-mobile-web-app-title" content="' . esc($name, 'attr') . '">' . "\n"
            . '  <meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n"
            . '  <link rel="manifest" href="' . esc($manifest, 'attr') . '">';
    }
}

if (! function_exists('catalog_url')) {
    function catalog_url(string $businessSlug, ?string $advisorSlug = null): string
    {
        $path = 'c/' . $businessSlug;

        if ($advisorSlug) {
            $path .= '/' . $advisorSlug;
        }

        return site_url($path);
    }
}

if (! function_exists('catalog_owner_return_url')) {
    /**
     * Back-to-panel URL when the visitor is logged in as this catalog's owner or as admin.
     * Guests (the public catalog link) get null so the bar is not rendered.
     */
    function catalog_owner_return_url(?array $business): ?string
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return null;
        }

        if (session()->get('user_role') === 'admin') {
            $id = (int) ($business['id'] ?? 0);

            return $id > 0 ? site_url('admin/negocios/' . $id) : site_url('admin');
        }

        $ownerId = (int) ($business['user_id'] ?? 0);

        return $ownerId > 0 && $ownerId === $userId ? site_url('panel') : null;
    }
}

if (! function_exists('uuid_v4')) {
    function uuid_v4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }
}

if (! function_exists('is_quote_uuid')) {
    function is_quote_uuid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, string $default = ''): string
    {
        return model(\App\Models\SettingModel::class)->getValue($key, $default);
    }
}

if (! function_exists('registration_is_open')) {
    function registration_is_open(): bool
    {
        return setting('registration_open', '0') === '1';
    }
}

if (! function_exists('business_is_public')) {
    function business_is_public(?array $business): bool
    {
        if ($business === null) {
            return false;
        }
        if ((int) ($business['is_active'] ?? 0) !== 1) {
            return false;
        }
        if ((int) ($business['suspended'] ?? 0) === 1) {
            return false;
        }
        if (($business['plan'] ?? 'trial') === 'trial' && ! empty($business['trial_ends_at'])) {
            if (strtotime($business['trial_ends_at']) < time()) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('business_can_operate')) {
    function business_can_operate(?array $business): bool
    {
        return business_is_public($business);
    }
}

if (! function_exists('business_plan_notice')) {
    /**
     * Returns null (no notice), or an array with keys: type, title, message.
     * type = 'warning' (about to expire) | 'expired' (already expired/blocked)
     *
     * @return array{type: string, title: string, message: string}|null
     */
    function business_plan_notice(?array $business): ?array
    {
        if ($business === null) {
            return null;
        }

        $plan = $business['plan'] ?? 'trial';
        $now  = time();

        if ((int) ($business['suspended'] ?? 0) === 1) {
            return [
                'type'    => 'expired',
                'title'   => 'Negocio suspendido',
                'message' => 'Tu catálogo fue suspendido por el administrador de la plataforma. Contacta al administrador para reactivar tu servicio.',
            ];
        }

        if ($plan === 'trial' && ! empty($business['trial_ends_at'])) {
            $ends = strtotime($business['trial_ends_at']);
            $days = (int) ceil(($ends - $now) / 86400);

            if ($days <= 0) {
                return [
                    'type'    => 'expired',
                    'title'   => 'Prueba gratuita vencida',
                    'message' => 'Tu período de prueba gratuita terminó. Contacta a quien te dio de alta en la plataforma para activar el plan y seguir usando el sistema.',
                ];
            }
            if ($days <= 5) {
                return [
                    'type'    => 'warning',
                    'title'   => 'Información del sistema',
                    'message' => 'Tu <strong>período de prueba gratuita vence el ' . date('d/m/Y', $ends) . '</strong>. Te invitamos a contactar a quien te dio de alta para activar el plan y seguir usando el sistema. Si ya regularizó, omita este mensaje.',
                ];
            }
        }

        if ($plan === 'active' && ! empty($business['next_billing_at'])) {
            $billing = strtotime($business['next_billing_at']);
            $days    = (int) ceil(($billing - $now) / 86400);

            if ($days <= 0) {
                return [
                    'type'    => 'warning',
                    'title'   => 'Información del sistema',
                    'message' => 'El <strong>pago oportuno de sostenimiento mensual venció el ' . date('d/m/Y', $billing) . '</strong>. Le invitamos a regularizar su servicio. Si ya pagó, omita este mensaje.',
                ];
            }
            if ($days <= 5) {
                return [
                    'type'    => 'warning',
                    'title'   => 'Información del sistema',
                    'message' => 'El <strong>pago oportuno de sostenimiento mensual vence el ' . date('d/m/Y', $billing) . '</strong>. Le invitamos a regularizar su servicio. Si ya pagó, omita este mensaje.',
                ];
            }
        }

        return null;
    }
}
