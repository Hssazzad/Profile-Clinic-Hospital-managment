<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NursingInvestigationPaymentItem extends Model
{
    protected $table = 'nursing_investigation_payment_items';

    protected $fillable = [
        'payment_id',
        'category',
        'service_name',
        'unit_price',
        'quantity',
        'discount',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount'   => 'decimal:2',
        'amount'     => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(NursingInvestigationPayment::class, 'payment_id');
    }
}
