<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'meeting_id', 'descripcion', 'completada'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
