<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonInvestigation extends Model
{
    protected $table = 'common_investigation';

    protected $fillable = [
        'name',
        'category',
        'description',
        'active',
    ];

    public $timestamps = false; // table has no created_at / updated_at
}
