<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferencePerson extends Model
{
    protected $table      = 'patient_ref';
    protected $primaryKey = 'ID';
    public    $timestamps = false;

    protected $fillable = [
        'Code',
        'ref_type',
        'Name',
        'Mobile',
        'active',
    ];
}