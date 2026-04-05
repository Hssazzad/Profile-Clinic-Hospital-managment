<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentMenu extends Model
{
    protected $table = 'parentmenu'; // legacy table
    public $timestamps = false;

    protected $fillable = ['parentcode', 'parentname', 'controllername'];

    public function submenus(): HasMany
    {
        // submenu.menuparentcode (int) -> parentmenu.parentcode (int)
        return $this->hasMany(SubMenu::class, 'menuparentcode', 'parentcode')
                    ->orderBy('position');
    }
}
