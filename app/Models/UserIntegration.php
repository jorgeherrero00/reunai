<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIntegration extends Model
{
    protected $fillable = [
        'user_id', 'tipo', 'token', 'config'
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
