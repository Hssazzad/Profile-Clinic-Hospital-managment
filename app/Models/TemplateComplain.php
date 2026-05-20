<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateComplain extends Model
{
    protected $table = 'template_complain'; // important

     protected $fillable = [
        'templateid',
        'complain',
        'note',
        'name',
    ];
}
