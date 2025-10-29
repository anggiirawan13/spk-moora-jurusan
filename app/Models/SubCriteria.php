<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCriteria extends Model
{
    use HasFactory;

    protected $table = 'sub_criterias';

    protected $fillable = [
        'criteria_id',
        'name',
        'value',
    ];

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }
}
