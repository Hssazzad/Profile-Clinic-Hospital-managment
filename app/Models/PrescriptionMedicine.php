<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionMedicine extends Model
{
    protected $table = 'prescription_items';   // <- migration table name
    protected $fillable = [
        'prescription_id','name','strength','dose','route','frequency','duration','timing'
    ];
    public $timestamps = true;

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
