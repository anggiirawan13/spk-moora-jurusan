<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $table = 'criterias';

    protected $fillable = [
        'code',
        'name',
        'weight',
        'attribute_type',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function alternativeValues()
    {
        return $this->hasMany(AlternativeValue::class, 'criteria_id');
    }

    public function subCriteria()
    {
        return $this->hasMany(SubCriteria::class);
    }
}
