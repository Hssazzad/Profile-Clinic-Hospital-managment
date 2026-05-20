<?php
// app/Models/Doctor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctors';
    
    protected $fillable = [
        'reg_no',
        'doctor_name',
        'speciality',
        'contact',
        'Posting',
        'RateCode',
        'active'  // active যোগ করুন
    ];
    
    // ডিফল্ট মান সেট করুন
    protected $attributes = [
        'active' => 1,
    ];
    
    public $timestamps = true;
}