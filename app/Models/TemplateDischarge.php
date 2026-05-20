<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDischarge extends Model
{
    protected $table = 'template_discharge';
    
    protected $fillable = [
        'templateid',
        'treatment',
        'condition',
        'follow_up',
        'active'
    ];
}