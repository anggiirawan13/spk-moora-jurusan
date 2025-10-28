<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $table = 'majors'; 

    protected $fillable = [
        'code',       
        'name',       
        'description',
        'image_name', 
    ];
    
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'major_id');
    }
}