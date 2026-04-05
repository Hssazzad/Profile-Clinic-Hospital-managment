<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigSpeciality extends Model
{
    protected $table = 'configspeciality';

    // Disable the automatic timestamp feature
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
    ];
}