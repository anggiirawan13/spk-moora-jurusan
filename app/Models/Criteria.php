<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    use HasFactory;

    protected $table = 'criterias';

    protected $fillable = [
        'major_id',
        'subject_id',
        'weight',
        'attribute_type',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function alternativeValues(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'criteria_id');
    }

    public function subCriteria(): HasMany
    {
        return $this->hasMany(SubCriteria::class);
    }
}
