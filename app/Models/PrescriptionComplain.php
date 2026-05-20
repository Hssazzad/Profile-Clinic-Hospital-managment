<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionComplain extends Model
{
    protected $table = 'prescriptions_complain'; // your table name
    protected $fillable = ['patientcode','prescription_id','name','name_normalized','active'];
    public $timestamps = true;
}
