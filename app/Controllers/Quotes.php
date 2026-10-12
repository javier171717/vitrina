<?php

namespace App\Controllers;

use App\Models\AdvisorModel;
use App\Models\BusinessModel;
use App\Models\ProductModel;
use App\Models\QuoteModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Quotes extends BaseController
{
    public function create(string $businessSlug, string $advisorSlug)
    {
        $business = model(BusinessModel::class)->findBySlug($businessSlug);
        $advisor  = $business
            ? model(AdvisorModel::class)->findBySlug((int) $business['id'], $advisorSlug)
            : null;

        if (! $business || ! $advisor || ! business_is_public($business)) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => 'Catálogo no encontrado.']);
        }

        $payload = [];
        try {
            $decoded = $this->request->getJSON(true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        } catch (\Throwable $e) {
            $raw = (string) $this->request->getBody();
            $decoded = json_decode($raw, true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        $rawItems = $payload['items'] ?? [];
        if (! is_array($rawItems) || $rawItems === []) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Agrega productos a la cotización.']);
        }

        if (count($rawItems) > 200) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Demasiados productos en la cotización.']);
        }

        $wanted = [];
        foreach ($rawItems as $row) {
            $id    = (int) ($row['id'] ?? 0);
            $qty   = (int) ($row['qty'] ?? 0);
            $color = trim(mb_substr((string) ($row['color'] ?? ''), 0, 40));
            $size  = trim(mb_substr((string) ($row['size'] ?? ''), 0, 40));
            if ($id < 1 || $qty < 1) {
                continue;
            }
            $key = $id . "\x1e" . mb_strtolower($color) . "\x1e" . mb_strtolower($size);
            if (! isset($wanted[$key])) {
                $wanted[$key] = ['id' => $id, 'qty' => 0, 'color' => $color, 'size' => $size];
            }
            $wanted[$key]['qty'] = min(9999, $wanted[$key]['qty'] + $qty);
        }

        if ($wanted === []) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Agrega productos a la cotización.']);
        }

        $ids = array_values(array_unique(array_map(static fn (array $row): int => $row['id'], $wanted)));

        $products = model(ProductModel::class)
            ->where('business_id', (int) $business['id'])
            ->where('is_active', 1)
            ->whereIn('id', $ids)
            ->findAll();

        $byId = [];
        foreach ($products as $product) {
            $byId[(int) $product['id']] = $product;
        }

        $lines = [];
        foreach ($wanted as $row) {
            $id = $row['id'];
            if (! isset($byId[$id])) {
                continue;
            }
            $allowedColors = parse_product_colors($byId[$id]['colors'] ?? null);
            $allowedSizes  = parse_product_colors($byId[$id]['sizes'] ?? null);
            $lines[] = [
                'product' => $byId[$id],
                'qty'     => $row['qty'],
                'color'   => $this->pickAllowed($allowedColors, $row['color']),
                'size'    => $this->pickAllowed($allowedSizes, $row['size']),
            ];
        }

        $forceWholesale = cart_uses_wholesale_total($lines, $business['wholesale_min_total'] ?? null);

        $items = [];
        foreach ($lines as $line) {
            $product = $line['product'];
            $qty     = $line['qty'];
            $price   = product_unit_price($product, $qty, $forceWholesale) ?? 0.0;
            $mayor   = product_is_wholesale($product, $qty, $forceWholesale);
            $items[] = [
                'sku'        => (string) ($product['sku'] ?? ''),
                'name'       => (string) $product['name'],
                'qty'        => $qty,
                'unit_price' => $price,
                'subtotal'   => round($qty * $price, 2),
                'wholesale'  => $mayor,
                'color'      => $line['color'],
                'size'       => $line['size'],
            ];
        }

        if ($items === []) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Esos productos ya no están disponibles.']);
        }

        $uuid = uuid_v4();
        $when = date('d/m/Y H:i');
        model(QuoteModel::class)->insert([
            'uuid'        => $uuid,
            'business_id' => (int) $business['id'],
            'advisor_id'  => (int) $advisor['id'],
            'items'       => json_encode($items, JSON_UNESCAPED_UNICODE),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'ok'   => true,
            'url'  => site_url('c/' . $uuid),
            'text' => $this->whatsappText($business, $items, $when),
        ]);
    }

    public function show(string $uuid): string
    {
        if (! is_quote_uuid($uuid)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $quote = model(QuoteModel::class)->findByUuid($uuid);
        if (! $quote) {
            throw PageNotFoundException::forPageNotFound();
        }

        $business = model(BusinessModel::class)->find((int) $quote['business_id']);
        if (! $business) {
            throw PageNotFoundException::forPageNotFound();
        }

        $advisor = model(AdvisorModel::class)->find((int) $quote['advisor_id']) ?: [];
        $items   = $this->normalizeItems($quote['items'] ?? []);
        $total   = 0.0;
        $hasPrices = false;
        $wholesale = false;

        foreach ($items as $item) {
            $total += (float) $item['subtotal'];
            if ((float) $item['unit_price'] > 0) {
                $hasPrices = true;
            }
            if (! empty($item['wholesale'])) {
                $wholesale = true;
            }
        }

        $created = $quote['created_at'] ?? '';
        $when    = $created !== '' ? date('d/m/Y H:i', strtotime((string) $created)) : date('d/m/Y H:i');

        return view('catalog/quote', [
            'business'  => $business,
            'advisor'   => $advisor,
            'items'     => $items,
            'total'     => $total,
            'hasPrices' => $hasPrices,
            'wholesale' => $wholesale,
            'when'      => $when,
        ]);
    }

    /**
     * @param mixed $raw
     * @return list<array{sku: string, name: string, qty: int, unit_price: float, subtotal: float, wholesale: bool, color: string, size: string}>
     */
    private function normalizeItems(mixed $raw): array
    {
        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }
        if (! is_array($raw)) {
            return [];
        }

        $items = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name      = trim((string) ($row['name'] ?? ''));
            $wholesale = ! empty($row['wholesale']);
            if (str_ends_with($name, ' (al por mayor)')) {
                $wholesale = true;
                $name      = trim(substr($name, 0, -15));
            }
            $qty      = max(1, (int) ($row['qty'] ?? 0));
            $price    = (float) ($row['unit_price'] ?? 0);
            $subtotal = isset($row['subtotal']) ? (float) $row['subtotal'] : round($qty * $price, 2);
            $items[]  = [
                'sku'        => (string) ($row['sku'] ?? ''),
                'name'       => $name !== '' ? $name : 'Producto',
                'qty'        => $qty,
                'unit_price' => $price,
                'subtotal'   => $subtotal,
                'wholesale'  => $wholesale,
                'color'      => trim((string) ($row['color'] ?? '')),
                'size'       => trim((string) ($row['size'] ?? '')),
            ];
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $business
     * @param list<array{sku: string, name: string, qty: int, unit_price: float, subtotal: float, wholesale: bool, color?: string, size?: string}> $items
     */
    private function whatsappText(array $business, array $items, string $when): string
    {
        $name = trim((string) ($business['name'] ?? ''));
        $city = trim((string) ($business['city'] ?? ''));
        $title = $city !== '' ? "{$name} ({$city})" : $name;

        $total     = 0.0;
        $hasPrices = false;
        $wholesale = false;
        foreach ($items as $item) {
            $total += (float) $item['subtotal'];
            if ((float) $item['unit_price'] > 0) {
                $hasPrices = true;
            }
            if (! empty($item['wholesale'])) {
                $wholesale = true;
            }
        }

        $lines   = [];
        $lines[] = '*' . $title . '*';
        $lines[] = '';
        $lines[] = 'Cotización';
        $lines[] = '';
        $lines[] = 'Cotización · ' . $when;
        $lines[] = '';
        $lines[] = '';
        $lines[] = 'Fecha: ' . $when;
        $lines[] = 'Canal: ' . ($wholesale ? 'Al por mayor' : 'Al detalle');
        $lines[] = '';
        $lines[] = '';

        foreach ($items as $item) {
            $label = $item['name'];
            $sku   = trim((string) ($item['sku'] ?? ''));
            if ($sku !== '') {
                $label .= ' · Ref. ' . $sku;
            }
            $parts = array_values(array_filter([
                (string) ($item['color'] ?? ''),
                (string) ($item['size'] ?? ''),
            ], static fn (string $value): bool => $value !== ''));
            if ($parts !== []) {
                $label .= ' (' . implode(', ', $parts) . ')';
            }
            if (! empty($item['wholesale'])) {
                $label .= ' (al por mayor)';
            }
            $row = '· ' . $label . ' — ' . (int) $item['qty'] . ' un';
            if ($hasPrices) {
                $row .= ' — ' . money_cop($item['subtotal']);
            }
            $lines[] = $row;
        }

        $lines[] = '';
        $lines[] = '';
        if ($hasPrices) {
            $lines[] = '*TOTAL: ' . money_cop($total) . '*';
            $lines[] = '';
        }
        $lines[] = '_Cotización (no es venta)_';
        $lines[] = '';
        $lines[] = '';
        $lines[] = '_Enviado desde ' . $name . '_';

        return implode("\n", $lines);
    }

    /**
     * @param list<string> $allowed
     */
    private function pickAllowed(array $allowed, string $value): string
    {
        if ($allowed === []) {
            return '';
        }
        foreach ($allowed as $option) {
            if (mb_strtolower($option) === mb_strtolower($value)) {
                return $option;
            }
        }

        return $allowed[0];
    }
}
