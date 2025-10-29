<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternative extends Model
{
    use HasFactory;

    protected $table = 'alternatives';

    protected $fillable = [
        'student_id'
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'alternative_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
}
