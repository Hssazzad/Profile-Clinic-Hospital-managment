<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class SurgeryTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'rx_admission',
        'pre_op_orders',
        'post_op_orders',
        'investigations',
        'advices',
        'notes',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'rx_admission' => 'array',
        'pre_op_orders' => 'array',
        'post_op_orders' => 'array',
        'investigations' => 'array',
        'advices' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationship to user who created the template
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope to get only active templates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get formatted template data for prescription
    public function getFormattedData()
    {
        return [
            'template_name' => $this->template_name,
            'rx_admission' => $this->rx_admission ?? [],
            'pre_op_orders' => $this->pre_op_orders ?? [],
            'post_op_orders' => $this->post_op_orders ?? [],
            'investigations' => $this->investigations ?? [],
            'advices' => $this->advices ?? [],
            'notes' => $this->notes
        ];
    }
}
