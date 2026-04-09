<?php
// app/Models/TemplateMedicine.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMedicine extends Model
{
    protected $table = 'template_medicine';
    
    protected $fillable = [
        'templeteid',
        'group',
        'name',
        'strength',
        'dose',
        'route',
        'frequency',
        'duration',
        'timing',
        'meal_timing',
        'morning',
        'noon',
        'night',
        'instruction',
        'company',
        'order_type',  // এই লাইনটি নিশ্চিত করুন
        'note',
        'active',
    ];
    
    // যদি timestamps ব্যবহার করেন
    // public $timestamps = true;
}