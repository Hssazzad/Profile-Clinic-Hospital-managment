<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    protected $table = 'submenu';
    public $timestamps = false;

    protected $fillable = [
        'menuparentcode', 'submenucode', 'submenuname', 'method', 'position',
    ];
}
