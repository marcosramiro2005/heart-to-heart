<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoHarmfulContent implements ValidationRule
{
    // Lista de términos ofensivos o dañinos en español
    private static array $terminos = [
        // Insultos y palabrotas comunes
        'puta', 'puto', 'putos', 'putas',
        'mierda', 'mierdas',
        'coño', 'joder', 'hostia', 'hostias',
        'gilipollas', 'capullo', 'capulla',
        'imbécil', 'imbecil', 'idiota', 'idiotas',
        'estúpido', 'estupido', 'estúpida', 'estupida',
        'cabrón', 'cabron', 'cabrona',
        'zorra', 'zorras',
        'subnormal', 'subnormales',
        'maricón', 'maricon', 'marica',
        'bastardo', 'bastarda',
        'hdp', 'hp', 'ptm',
        'asqueroso', 'asquerosa',
        'maldito', 'maldita',
        'imbéciles', 'imbeciles',
        'inútil', 'inutl',
        'retrasado', 'retrasada',
        'mongolo', 'mongola',
        'perra',
        'gilipolla',
        'gilipollez',
        'cretino', 'cretina',
        'pedazo de mierda',
        'vete a la mierda',
        'vete al infierno',
        'hijo de puta',
        'hija de puta',
        // Mensajes que fomentan daño
        'suicidate', 'suicídate',
        'mátate', 'matate',
        'muérete', 'muerate',
        'date muerte',
        'hazle daño',
        'hazte daño',
        'córtate', 'cortate',
        'te mereces morir',
        'ojalá te mueras',
        'ojala te mueras',
        // Discriminación y odio
        'negro de mierda',
        'moro de mierda',
        'sudaca',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalizar: minúsculas y quitar acentos para evitar evasiones simples
        $texto = $this->normalizar((string) $value);

        foreach (self::$terminos as $termino) {
            $terminoNorm = $this->normalizar($termino);

            // Para frases (con espacios) buscamos la subcadena directamente
            // Para palabras sueltas usamos límites de palabra (\b) con Unicode
            if (str_contains($terminoNorm, ' ')) {
                if (str_contains($texto, $terminoNorm)) {
                    $fail('Por favor, usa un lenguaje respetuoso. Este espacio es para apoyarnos mutuamente. 💚');
                    return;
                }
            } else {
                $patron = '/\b' . preg_quote($terminoNorm, '/') . '\b/u';
                if (preg_match($patron, $texto)) {
                    $fail('Por favor, usa un lenguaje respetuoso. Este espacio es para apoyarnos mutuamente. 💚');
                    return;
                }
            }
        }
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower($texto, 'UTF-8');

        // Eliminar acentos para que "estúpido" y "estupido" sean equivalentes
        $mapa = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n',
        ];

        return strtr($texto, $mapa);
    }
}
