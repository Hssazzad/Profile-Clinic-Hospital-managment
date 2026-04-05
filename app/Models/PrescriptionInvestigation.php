<?php
// app/Models/PrescriptionInvestigation.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrescriptionInvestigation extends Model
{
      protected $table = 'prescriptions_investigations'; 
    protected $fillable = ['prescription_id','investigation_id','name','note'];

    public function prescription() { return $this->belongsTo(Prescription::class); }
    public function master() { return $this->belongsTo(CommonInvestigation::class, 'investigation_id'); }
}
