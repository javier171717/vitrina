<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

class Uploader
{
    /**
     * @var list<string>
     */
    private array $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function store(?UploadedFile $file, int $businessId, string $subdir = 'files'): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension() ?: '');
        if (! in_array($ext, $this->allowed, true)) {
            throw new RuntimeException('Usa una imagen JPG, PNG, WEBP o GIF.');
        }

        $dir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $businessId . DIRECTORY_SEPARATOR . $subdir;
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('No se pudo crear la carpeta de archivos.');
        }

        $safeExt = $ext === 'jpeg' ? 'jpg' : $ext;
        $name    = bin2hex(random_bytes(8)) . '.' . $safeExt;
        $file->move($dir, $name);

        return 'uploads/' . $businessId . '/' . $subdir . '/' . $name;
    }

    public function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $uploads = realpath(rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads');
        $full    = realpath(rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . str_replace(['\\', '..'], ['/', ''], $relativePath));

        if ($uploads && $full && str_starts_with($full, $uploads) && is_file($full)) {
            unlink($full);
        }
    }
}
