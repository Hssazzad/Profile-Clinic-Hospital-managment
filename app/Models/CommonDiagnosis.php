<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonDiagnosis extends Model
{
    protected $table = 'common_diagnosis';
    protected $fillable = ['code','name','name_normalized','active'];
    public $timestamps = true;
}
