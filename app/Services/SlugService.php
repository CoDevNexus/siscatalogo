<?php
namespace App\Services;

class SlugService
{
    /**
     * Genera un slug único y limpio a partir de una cadena
     */
    public static function generate(string $text): string
    {
        // Reemplazar caracteres no alfanuméricos por guiones
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterar (quitar acentos)
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Quitar caracteres no deseados
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Quitar guiones duplicados
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
