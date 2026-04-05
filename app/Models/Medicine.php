<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'company_name',
        'strength',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope to get only active medicines
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get medicines by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Get formatted display name
    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        if ($this->strength) {
            $name .= " ({$this->strength})";
        }
        return $name;
    }
}
