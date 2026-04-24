<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_no', 'patient_id', 'prescription_id', 'appointment_id',
        'total_amount', 'discount', 'payable_amount', 'paid_amount',
        'due_amount', 'refund_amount', 'payment_status', 'payment_date',
        'remarks', 'created_by', 'updated_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
}