<?php

// app/Models/CommonMedicine.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonMedicine extends Model
{
    protected $table = 'common_medicine';

    protected $fillable = [
        'code',
        'name',
        'GroupName',
        'strength',
        'name_normalized',
        'active',
    ];
}
