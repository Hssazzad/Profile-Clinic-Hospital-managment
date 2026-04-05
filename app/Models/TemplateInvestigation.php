<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateInvestigation extends Model
{
    protected $table = 'template_investigations'; // correct table for investigations

     protected $fillable = [
        'templateid',
        'investigation_id',
        'name',
        'note',
        'updated_at',
        'created_at'
    ];
}
