<?php
// app/Services/UsageService.php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class UsageService
{
    /**
     * Límites por plan
     */
    private const LIMITS = [
        'free' => [
            'meetings_per_month' => 1,
            'max_duration_minutes' => 30,
            'features' => [
                'sentiment_analysis' => false,
                'behavioral_insights' => false,
                'export_pdf' => false,
                'integrations' => false,
            ]
        ],
        'starter' => [
            'meetings_per_month' => 5,
            'max_duration_minutes' => 60,
            'features' => [
                'sentiment_analysis' => true,
                'behavioral_insights' => false,
                'export_pdf' => true,
                'integrations' => true,
            ]
        ],
        'pro' => [
            'meetings_per_month' => 20,
            'max_duration_minutes' => 120,
            'features' => [
                'sentiment_analysis' => true,
                'behavioral_insights' => true,
                'export_pdf' => true,
                'integrations' => true,
            ]
        ],
    ];

    /**
     * Obtener el plan actual del usuario
     */
    public function getUserPlan(User $user): string
    {
        $subscription = $user->subscription;
        
        // Si tiene suscripción activa, usar ese plan
        if ($subscription && $subscription->status === 'active') {
            return $subscription->plan;
        }
        
        // Si la suscripción está cancelada pero aún vigente
        if ($subscription && $subscription->canceled_at && $subscription->ends_at && $subscription->ends_at->isFuture()) {
            return $subscription->plan;
        }
        
        // Por defecto, free
        return 'free';
    }

    /**
     * Verificar si el usuario puede subir una reunión
     */
    public function canUploadMeeting(User $user): array
    {
        $plan = $this->getUserPlan($user);
        $limit = self::LIMITS[$plan]['meetings_per_month'];
        
        $meetingsThisMonth = $this->getMeetingsThisMonth($user);
        
        if ($meetingsThisMonth >= $limit) {
            return [
                'allowed' => false,
                'reason' => 'monthly_limit_reached',
                'current' => $meetingsThisMonth,
                'limit' => $limit,
                'plan' => $plan,
                'next_reset' => Carbon::now()->endOfMonth()->addDay()->format('d/m/Y'),
                'message' => $this->getLimitMessage($plan, $meetingsThisMonth, $limit),
            ];
        }

        return [
            'allowed' => true,
            'current' => $meetingsThisMonth,
            'limit' => $limit,
            'remaining' => $limit - $meetingsThisMonth,
            'plan' => $plan,
        ];
    }

    /**
     * Verificar si la duración es válida para el plan
     */
    public function canUploadDuration(User $user, int $durationMinutes): array
    {
        $plan = $this->getUserPlan($user);
        $maxDuration = self::LIMITS[$plan]['max_duration_minutes'];
        
        if ($durationMinutes > $maxDuration) {
            return [
                'allowed' => false,
                'reason' => 'duration_exceeded',
                'duration' => $durationMinutes,
                'max_duration' => $maxDuration,
                'plan' => $plan,
                'message' => "Tu plan {$plan} permite reuniones de máximo {$maxDuration} minutos. Este archivo tiene {$durationMinutes} minutos.",
            ];
        }

        return [
            'allowed' => true,
            'duration' => $durationMinutes,
            'max_duration' => $maxDuration,
            'plan' => $plan,
        ];
    }

    /**
     * Verificar si puede acceder a una feature
     */
    public function canAccessFeature(User $user, string $feature): bool
    {
        $plan = $this->getUserPlan($user);
        return self::LIMITS[$plan]['features'][$feature] ?? false;
    }

    /**
     * Obtener todas las features del plan
     */
    public function getPlanFeatures(User $user): array
    {
        $plan = $this->getUserPlan($user);
        return self::LIMITS[$plan]['features'];
    }

    /**
     * Obtener límites del plan
     */
    public function getPlanLimits(string $plan): array
    {
        return self::LIMITS[$plan] ?? self::LIMITS['free'];
    }

    /**
     * Obtener reuniones del mes actual
     */
    private function getMeetingsThisMonth(User $user): int
    {
        return $user->meetings()
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
    }

    /**
     * Mensaje personalizado según el límite alcanzado
     */
    private function getLimitMessage(string $plan, int $current, int $limit): string
    {
        $messages = [
            'free' => "Has usado tu reunión gratuita de este mes. Espera hasta el {$this->getNextResetDate()} o actualiza a Starter para 5 reuniones/mes.",
            'starter' => "Has alcanzado tu límite de {$limit} reuniones/mes. Actualiza a Pro para procesar hasta 20 reuniones/mes.",
            'pro' => "Has alcanzado el límite de {$limit} reuniones/mes. El límite se reiniciará el {$this->getNextResetDate()}.",
        ];

        return $messages[$plan] ?? $messages['free'];
    }

    /**
     * Próxima fecha de reinicio
     */
    private function getNextResetDate(): string
    {
        return Carbon::now()->endOfMonth()->addDay()->format('d/m/Y');
    }

    /**
     * Obtener estadísticas de uso del usuario
     */
    public function getUserStats(User $user): array
    {
        $plan = $this->getUserPlan($user);
        $limits = $this->getPlanLimits($plan);
        $meetingsThisMonth = $this->getMeetingsThisMonth($user);
        
        return [
            'plan' => $plan,
            'meetings_used' => $meetingsThisMonth,
            'meetings_limit' => $limits['meetings_per_month'],
            'meetings_remaining' => max(0, $limits['meetings_per_month'] - $meetingsThisMonth),
            'max_duration' => $limits['max_duration_minutes'],
            'features' => $limits['features'],
            'next_reset' => $this->getNextResetDate(),
            'usage_percentage' => round(($meetingsThisMonth / $limits['meetings_per_month']) * 100, 1),
        ];
    }
}