<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_no',
        'patient_id',
        'type',
        'prescription_date',
        'doctor_name',
        'primary_diagnosis',
        'secondary_diagnosis',
        'final_diagnosis',
        'lab_investigations',
        'radiology',
        'other_investigations',
        'medications',
        'pre_op_instructions',
        'post_op_instructions',
        'follow_up',
        'discharge_advice',
        'doctor_notes',
        'discharge_date',
        'discharge_type',
        'anesthesia_clearance',
        'created_by',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'discharge_date' => 'date',
    ];

    /**
     * Get the admission that owns the prescription.
     */
    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'patient_id');
    }

    /**
     * Get the user who created the prescription.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted type label
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'pre-op' => 'Pre-Operative',
            'post-op' => 'Post-Operative',
            'fresh' => 'Fresh Order',
            'discharge' => 'Discharge'
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get color for type badge
     */
    public function getTypeColorAttribute(): string
    {
        $colors = [
            'pre-op' => 'warning',
            'post-op' => 'info',
            'fresh' => 'secondary',
            'discharge' => 'danger'
        ];

        return $colors[$this->type] ?? 'primary';
    }

    /**
     * Scope for today's prescriptions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('prescription_date', today());
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
