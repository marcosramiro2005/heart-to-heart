<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
{
    $resources = [
        [
            'title' => '5 técnicas para reducir la ansiedad',
            'content' => 'La ansiedad es una respuesta natural del cuerpo...',
            'category' => 'ansiedad',
        ],
        [
            'title' => 'La importancia del sueño en tu salud mental',
            'content' => 'Dormir bien es fundamental para el bienestar emocional...',
            'category' => 'sueño',
        ],
        [
            'title' => 'Cómo practicar la gratitud diariamente',
            'content' => 'El diario de gratitud es una práctica respaldada por la ciencia...',
            'category' => 'bienestar',
        ],
    ];

    foreach ($resources as $resource) {
        \App\Models\Resource::create($resource);
    }
}
}
