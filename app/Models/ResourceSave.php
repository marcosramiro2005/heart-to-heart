<?php

namespace App\Models;

// Tabla pivote entre usuarios y recursos: registra qué recursos ha guardado cada usuario.
// Un registro en esta tabla = el usuario con user_id ha guardado el recurso con resource_id.
// El toggle en ResourceLibraryController crea o elimina esta fila según el estado actual.

use Illuminate\Database\Eloquent\Model;

class ResourceSave extends Model
{
    // user_id: usuario que guardó el recurso — resource_id: recurso guardado
    protected $fillable = ['user_id', 'resource_id'];
}