@component('mail::message')
# 🧠 Resumen de tu reunión: {{ $meeting->titulo }}

**Fecha:** {{ $meeting->created_at->format('d/m/Y H:i') }}

---

## 📋 Resumen
{!! $meeting->resumen !!}

---

@if($meeting->sentiment_analysis)
## 😊 Análisis de Sentimiento

@php
    $sentiment = $meeting->sentiment_analysis;
    $positivo = $sentiment['positivo'] ?? 0;
    $neutral = $sentiment['neutral'] ?? 0;
    $critico = $sentiment['critico'] ?? 0;
    
    // Determinar el dominante
    $max = max($positivo, $neutral, $critico);
    if ($max == $positivo) {
        $emoji = '✅';
        $mensaje = 'Ambiente positivo';
    } elseif ($max == $critico) {
        $emoji = '⚠️';
        $mensaje = 'Puntos críticos detectados';
    } else {
        $emoji = 'ℹ️';
        $mensaje = 'Tono neutral';
    }
@endphp

**{{ $emoji }} {{ $mensaje }}**

- 😊 **Positivo:** {{ $positivo }}%
- 📊 **Neutral:** {{ $neutral }}%
- ⚠️ **Crítico:** {{ $critico }}%

@if(isset($sentiment['resumen_sentimiento']))
> {{ $sentiment['resumen_sentimiento'] }}
@endif

---
@endif

@if($meeting->insight)
## 💡 Insight Conductual
{!! $meeting->insight !!}
@endif

---

@if($meeting->tasks->count() > 0)
## ✅ Tareas Extraídas
@foreach($meeting->tasks as $task)
- {{ $task->descripcion }}
@endforeach
@endif

---

@component('mail::button', ['url' => route('reuniones.show', $meeting)])
Ver reunión completa
@endcomponent

Gracias por usar **Sumora** ⚡  
*Transforma tus reuniones en acciones concretas*

@endcomponent