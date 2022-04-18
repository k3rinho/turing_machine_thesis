<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;
    protected $table = 'machines';
    protected $fillable = ['name', 'user_id','data','task_id'];

    public function getCreatedAtAttribute($value)
    { 
         return $this->asDateTime($value)->format('Y-m-d H:i'); 
    }

    public function getUpdatedAtAttribute($value)
    { 
         return $this->asDateTime($value)->format('Y-m-d H:i'); 
    }

     public function task()
     {
          return $this->belongsTo('App\Models\Task', 'task_id', 'id');
     }

     public function user()
     {
          return $this->belongsTo('App\Models\User', 'user_id', 'id');
     }
}
