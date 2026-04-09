<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NursingInvestigationPayment extends Model
{
    protected $table = 'nursing_investigation_payments';

    protected $fillable = [
        'admission_id',
        'patient_id',
        'patient_name',
        'patient_code',
        'patient_age',
        'mobile_no',
        'payment_type',
        'payment_date',
        'collected_by',
        'total_amount',
        'discount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'transaction_ref',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount'     => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_amount'   => 'decimal:2',
    ];

    // -- Relations ------------------------------------------------

    public function items(): HasMany
    {
        return $this->hasMany(NursingInvestigationPaymentItem::class, 'payment_id');
    }

    // -- Scopes ---------------------------------------------------

    public function scopePartial($query)
    {
        return $query->where('payment_type', 'partial');
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // -- Helpers --------------------------------------------------

    public static function totalPaidForPatient($patientId): float
    {
        return (float) static::where('patient_id', $patientId)
            ->where('payment_type', 'partial')
            ->sum('paid_amount');
    }

    public static function partialSummaryForPatient($patientId)
    {
        return static::with('items')
            ->where('patient_id', $patientId)
            ->where('payment_type', 'partial')
            ->orderBy('payment_date')
            ->get();
    }
}