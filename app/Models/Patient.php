<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $primaryKey = 'id';

	protected $fillable = [
    'patientcode','patientname','patientfather','patienthusband','age',
    'upozila','district','village','photo','union',
    'spomobile_no','date_of_birth','mobile_no','relmobile_no','nid_number','email',
    'gender','blood_group','notes',
    'reference_type','reference_person','reference_name'
];

    protected $casts = [
        'operationdate' => 'date',
        'operationtime' => 'datetime:H:i',
        'date_of_birth' => 'date',
    ];
}
