<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id','prescription_no','prescribed_on',
        'doctor_name','doctor_reg_no','chief_complaint',
        'diagnosis','advices','investigations',
    ];

    protected $casts = [
        'prescribed_on' => 'date',
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function items() {
        return $this->hasMany(PrescriptionItem::class);
    }
}
