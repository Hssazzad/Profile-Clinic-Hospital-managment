<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'investigation_id',
        'amount',
        'date',
        'method',
        'reference',
        'notes',
        'recorded_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function investigation()
    {
        return $this->belongsTo(Investigation::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
?>