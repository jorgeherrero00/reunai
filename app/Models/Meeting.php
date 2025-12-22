<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'user_id', 'titulo', 'archivo', 'transcripcion', 'resumen','formato_origen', 'guardar_en_google_sheets', 'insight', 'sentiment_analysis'
    ];

    protected $casts = [
        'sentiment_analysis' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    
    // Helper para obtener el sentimiento dominante
    public function getSentimentoDominanteAttribute()
    {
        if (!$this->sentiment_analysis) return null;
        
        $max = max($this->sentiment_analysis);
        return array_search($max, $this->sentiment_analysis);
    }
}
