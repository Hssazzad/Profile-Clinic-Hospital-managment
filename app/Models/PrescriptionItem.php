<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id','medicine_name','strength','dose',
        'route','frequency','duration','timing','note',
    ];

    public function prescription() {
        return $this->belongsTo(Prescription::class);
    }
}
