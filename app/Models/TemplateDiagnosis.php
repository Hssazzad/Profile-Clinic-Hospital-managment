<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Template;


class TemplateDiagnosis extends Model
{
    protected $table = 'template_diagnosis';

    protected $fillable = [
        'templateid',
        'name',
        'note',
        'active',
        'name_normalized',
    ];
}
