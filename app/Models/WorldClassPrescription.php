<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorldClassPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_no',
        'patient_id',
        'prescription_type',
        'prescribed_on',
        'doctor_name',
        'doctor_id',
        'bp_systolic',
        'bp_diastolic',
        'pulse',
        'spo2',
        'temperature',
        'respiration',
        // investigation/general data
        'investigations',
        'case_summary',
        'ot_time',
        'consent_taken',
        // admission/notes
        'admission_notes',
        // operation specific
        'preop_diagnosis',
        'preop_instructions',
        'postop_diagnosis',
        'postop_instructions',
        'fresh_notes',
        'discharge_diagnosis',
        'discharge_advice',
        'doctor_notes',
        // optional baby/discharge columns (may not be in every form)
        'baby_sex',
        'baby_weight',
        'birth_time',
        'accounts_clearance',
        'final_bill_amount',
    ];

    protected $casts = [
        'prescribed_on' => 'date',
        'temperature' => 'decimal:1',
        'ot_time' => 'time',
        'consent_taken' => 'boolean',
        'accounts_clearance' => 'boolean',
        'final_bill_amount' => 'decimal:2',
    ];

    /**
     * Get the patient that owns the prescription.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor who created the prescription.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the medicines for this prescription.
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(WorldClassPrescriptionMedicine::class)
            ->orderBy('sort_order');
    }

    /**
     * Get formatted type label
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'outdoor' => 'Outdoor (Rx)',
            'admission' => 'Admission Orders',
            'pre-op' => 'Pre-Operative',
            'post-op' => 'Post-Operative',
            'fresh' => 'Fresh Orders',
            'discharge' => 'Discharge Summary'
        ];

        return $labels[$this->prescription_type] ?? ucfirst($this->prescription_type);
    }

    /**
     * Get color for type badge
     */
    public function getTypeColorAttribute(): string
    {
        $colors = [
            'outdoor' => 'primary',
            'admission' => 'info',
            'pre-op' => 'warning',
            'post-op' => 'success',
            'fresh' => 'secondary',
            'discharge' => 'danger'
        ];

        return $colors[$this->prescription_type] ?? 'primary';
    }

    /**
     * Get formatted vitals
     */
    public function getFormattedVitalsAttribute(): string
    {
        $vitals = [];

        if ($this->bp_systolic && $this->bp_diastolic) {
            $vitals[] = "BP: {$this->bp_systolic}/{$this->bp_diastolic} mmHg";
        }

        if ($this->pulse) {
            $vitals[] = "Pulse: {$this->pulse} bpm";
        }

        if ($this->spo2) {
            $vitals[] = "SpO2: {$this->spo2}%";
        }

        if ($this->temperature) {
            $vitals[] = "Temp: {$this->temperature}°C";
        }

        if ($this->respiration) {
            $vitals[] = "RR: {$this->respiration} rpm";
        }

        return implode(' | ', $vitals);
    }

    /**
     * Scope for today's prescriptions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('prescribed_on', today());
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('prescription_type', $type);
    }

    /**
     * Scope for specific patient
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
}

class WorldClassPrescriptionMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medicine_name',
        'dosage',
        'duration',
        'sort_order',
    ];

    /**
     * Get the prescription that owns the medicine.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(WorldClassPrescription::class);
    }
}
