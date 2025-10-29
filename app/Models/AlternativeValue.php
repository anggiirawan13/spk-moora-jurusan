<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlternativeValue extends Model
{
    use HasFactory;

    protected $table = 'alternative_values';

    protected $fillable = [
        'alternative_id',
        'sub_criteria_id',
        'value'
    ];

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class, 'alternative_id');
    }

    public function subCriteria(): BelongsTo
    {
        return $this->belongsTo(SubCriteria::class, 'sub_criteria_id');
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }
}
