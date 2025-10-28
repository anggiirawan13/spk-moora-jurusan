<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;

    protected $table = 'alternatives';

    protected $fillable = ['car_id'];

    public function values()
    {
        return $this->hasMany(AlternativeValue::class, 'alternative_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}

