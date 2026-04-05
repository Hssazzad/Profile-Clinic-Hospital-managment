<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMenu extends Model
{
    protected $table = 'usermenu';
    public $timestamps = false;

    protected $fillable = ['userpin', 'menuparentcode', 'position'];
}
