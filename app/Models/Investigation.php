<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'admission_id',
        'type',
        'date',
        'charge',
        'status',
        'notes',
        'created_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function admission()
    {
        return $this->belongsTo(PatientAdmission::class);
    }

    public function payments()
    {
        return $this->hasMany(InvestigationPayment::class);
    }
}
?>