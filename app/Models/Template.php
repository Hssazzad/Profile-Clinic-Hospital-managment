<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'tbl_template'; // important

    protected $fillable = [
        'templateid',
        'title',
        'description',
        'status',
    ];
    
    /**
     * Relationship with TemplateMedicine
     * একটি টেমপ্লেটের অনেকগুলো medicine থাকতে পারে
     * foreign key: templeteid (TemplateMedicine table)
     * local key: templateid (Template table)
     */
    public function medicines()
    {
        return $this->hasMany(TemplateMedicine::class, 'templeteid', 'templateid');
    }
    
    /**
     * AT ADMISSION medicines (order_type = admit)
     */
    public function admitMedicines()
    {
        return $this->hasMany(TemplateMedicine::class, 'templeteid', 'templateid')
                    ->where('order_type', 'admit');
    }
    
    /**
     * PRE-OPERATION medicines (order_type = preorder)
     */
    public function preorderMedicines()
    {
        return $this->hasMany(TemplateMedicine::class, 'templeteid', 'templateid')
                    ->where('order_type', 'preorder');
    }
    
    /**
     * POST-OPERATION medicines (order_type = postorder)
     */
    public function postorderMedicines()
    {
        return $this->hasMany(TemplateMedicine::class, 'templeteid', 'templateid')
                    ->where('order_type', 'postorder');
    }
    
    /**
     * Relationship with TemplateDiagnosis
     */
    public function diagnoses()
    {
        return $this->hasMany(TemplateDiagnosis::class, 'templateid', 'templateid');
    }
    
    /**
     * Relationship with TemplateInvestigation
     */
    public function investigations()
    {
        return $this->hasMany(TemplateInvestigation::class, 'templateid', 'templateid');
    }
    
    /**
     * Relationship with TemplateAdvice
     */
    public function advices()
    {
        return $this->hasMany(TemplateAdvice::class, 'templateid', 'templateid');
    }
    
    /**
     * Relationship with TemplateComplain
     */
    public function complains()
    {
        return $this->hasMany(TemplateComplain::class, 'templateid', 'templateid');
    }
    
    /**
     * Relationship with TemplateDischarge (usually one per template)
     */
    public function discharge()
    {
        return $this->hasOne(TemplateDischarge::class, 'templateid', 'templateid');
    }
    
    /**
     * Get total medicines count
     */
    public function getTotalMedicinesCountAttribute()
    {
        return $this->medicines()->count();
    }
    
    /**
     * Get admit medicines count
     */
    public function getAdmitCountAttribute()
    {
        return $this->admitMedicines()->count();
    }
    
    /**
     * Get preorder medicines count
     */
    public function getPreorderCountAttribute()
    {
        return $this->preorderMedicines()->count();
    }
    
    /**
     * Get postorder medicines count
     */
    public function getPostorderCountAttribute()
    {
        return $this->postorderMedicines()->count();
    }
}
