<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubMenu extends Model
{
    protected $table = 'user_sub_menu';
    public $timestamps = false;

    protected $fillable = ['userpin', 'menuparentcode', 'submenucode', 'position'];
}
