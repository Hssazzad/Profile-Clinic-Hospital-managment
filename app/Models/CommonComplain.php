<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonComplain extends Model
{
    protected $table = 'common_complain'; // your table name
    protected $fillable = ['name','name_normalized','active'];
    public $timestamps = true;
}
