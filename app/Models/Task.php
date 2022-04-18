<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = "tasks";

    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'io',
    ];
    // task has one user by user_id
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id','creator_id');
    }

    public function getCreatedAtAttribute($value)
    { 
         return $this->asDateTime($value)->format('Y-m-d H:i'); 
    }
    
    public function getUpdatedAtAttribute($value)
    { 
         return $this->asDateTime($value)->format('Y-m-d H:i'); 
    }
}
