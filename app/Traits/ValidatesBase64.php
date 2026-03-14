<?php

namespace App\Traits;

use Closure;

trait ValidatesBase64
{
    /**
     * Regla para validar una IMAGEN en base64 (PNG/JPG/JPEG).
     *
     * @param int $maxMB  Tamaño máximo permitido en MB (default 100)
     * @param array $allowedMimes  MIMEs permitidos
     * @return array
     */
    public function base64ImageRule(
        int $maxMB = 100,
        array $allowedMimes = ['image/png','image/jpeg','image/jpg']
    ): array {
        return [
            'nullable',
            // (opcional) valida estructura base64 con o sin prefijo
            'regex:/^(data:image\/[a-zA-Z0-9.+-]+;base64,)?[A-Za-z0-9+\/=]+$/',
            function (string $attribute, $value, Closure $fail) use ($maxMB, $allowedMimes) {
                if ($value === null || $value === '') {
                    return; // es nullable
                }

                // Normalizar
                $raw = trim((string)$value);
                $raw = str_replace(' ', '+', $raw);

                // Quitar prefijo si viene
                if (preg_match('/^data:image\/([a-zA-Z0-9.+-]+);base64,/', $raw) === 1) {
                    $raw = substr($raw, strpos($raw, ',') + 1);
                }

                // Padding base64
                if ($raw === '' || (strlen($raw) % 4) !== 0) {
                    return $fail('El contenido base64 no tiene el formato correcto.');
                }

                // Decodificar
                $decoded = base64_decode($raw, true);
                if ($decoded === false) {
                    return $fail('El contenido base64 no se pudo decodificar.');
                }

                // Debe ser imagen real
                $info = @getimagesizefromstring($decoded);
                if ($info === false) {
                    return $fail('El contenido no corresponde a una imagen válida.');
                }

                // MIME permitido
                $mime = strtolower($info['mime'] ?? '');
                if (!in_array($mime, $allowedMimes, true)) {
                    return $fail('Solo se permiten imágenes en formato PNG, JPG o JPEG.');
                }

                // Tamaño máximo
                $maxBytes = $maxMB * 1024 * 1024;
                if (strlen($decoded) > $maxBytes) {
                    return $fail("La imagen base64 no debe exceder los {$maxMB} MB.");
                }
            },
        ];
    }

    /**
     * Regla para validar un PDF en base64.
     *
     * @param int $maxMB  Tamaño máximo permitido en MB (default 100)
     * @return array
     */
    public function base64PdfRule(int $maxMB = 100): array
    {
        return [
            'nullable',
            // (opcional) valida estructura base64 con o sin prefijo
            'regex:/^(data:application\/pdf;base64,)?[A-Za-z0-9+\/=]+$/',
            function (string $attribute, $value, Closure $fail) use ($maxMB) {
                if ($value === null || $value === '') {
                    return; // es nullable
                }

                $raw = trim((string)$value);
                $raw = str_replace(' ', '+', $raw);

                // Quitar prefijo si viene
                if (preg_match('/^data:application\/pdf;base64,/', $raw) === 1) {
                    $raw = substr($raw, strpos($raw, ',') + 1);
                }

                // Padding base64
                if ($raw === '' || (strlen($raw) % 4) !== 0) {
                    return $fail('El contenido base64 no tiene el formato correcto.');
                }

                // Decodificar
                $decoded = base64_decode($raw, true);
                if ($decoded === false) {
                    return $fail('El contenido base64 no se pudo decodificar.');
                }

                // Detectar MIME por contenido
                $mime = null;
                if (function_exists('finfo_buffer')) {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mime = strtolower($finfo->buffer($decoded)) ?: null;
                }

                // Fallback por firma %PDF si no hay finfo
                if ($mime === null) {
                    $startsWithPdf = strncmp($decoded, "%PDF", 4) === 0;
                    if (!$startsWithPdf) {
                        return $fail('El contenido no corresponde a un PDF válido.');
                    }
                    $mime = 'application/pdf';
                }

                if ($mime !== 'application/pdf') {
                    return $fail('Solo se permite un archivo en formato PDF.');
                }

                // Tamaño máximo
                $maxBytes = $maxMB * 1024 * 1024;
                if (strlen($decoded) > $maxBytes) {
                    return $fail("El PDF base64 no debe exceder los {$maxMB} MB.");
                }
            },
        ];
    }
}
