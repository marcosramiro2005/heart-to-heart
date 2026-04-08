<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceSave extends Model
{
    protected $fillable = ['user_id', 'resource_id'];
}