<?php
// app/Http/Controllers/StripeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

        public function checkout(Request $request)
{
    $plan = $request->plan;
    $user = auth()->user();
    $subscription = $user->subscription;
    
    $prices = [
        'starter' => env('STRIPE_PRICE_STARTER'),
        'pro' => env('STRIPE_PRICE_PRO'),
    ];

    if (!isset($prices[$plan])) {
        return back()->with('error', 'Plan no válido');
    }

    // ✅ VALIDACIÓN 1: Ya tiene este plan activo
    if ($subscription && $subscription->plan === $plan && $subscription->status === 'active') {
        return redirect()->route('subscription.manage')->with('info', 'Ya tienes el plan ' . ucfirst($plan) . ' activo');
    }

    // ✅ VALIDACIÓN 2: Ya tiene suscripción - debe usar changePlan
    if ($subscription && $subscription->stripe_subscription_id && $subscription->status === 'active') {
        return redirect()->route('subscription.manage')->with('info', 'Ya tienes una suscripción activa. Cambia de plan desde tu panel de gestión.');
    }

    try {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $prices[$plan],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'customer_email' => $user->email,
            'metadata' => [
                'user_id' => $user->id,
                'plan' => $plan,
                'price_id' => $prices[$plan],
            ],
        ]);

        return redirect($session->url);
    } catch (\Exception $e) {
        return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
    }
}

    public function success(Request $request)
{
    $sessionId = $request->session_id;

    if (!$sessionId) {
        return redirect()->route('dashboard')->with('error', 'Sesión no válida');
    }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        
        \Log::info('Checkout session retrieved: ' . json_encode($session));
        
        $user = auth()->user();
        
        $priceId = $session->metadata->price_id ?? null;
        
        if (!$priceId) {
            throw new \Exception('No se pudo obtener el price_id de la suscripción');
        }
        
        // ✅ Actualizar en tabla users
        $user->update([
            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
            'plan' => $session->metadata->plan,
            'subscription_ends_at' => now()->addMonth(),
        ]);

        // ✅ Actualizar en tabla subscriptions
        \App\Models\Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stripe_customer_id' => $session->customer,
                'stripe_subscription_id' => $session->subscription,
                'stripe_price_id' => $priceId,
                'plan' => $session->metadata->plan,
                'status' => 'active',
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
                'canceled_at' => null,
            ]
        );

        \Log::info('Subscription created/updated for user: ' . $user->id);

        return view('checkout.success', ['plan' => $session->metadata->plan]);
        
    } catch (\Exception $e) {
        \Log::error('Error en checkout.success: ' . $e->getMessage());
        return redirect()->route('dashboard')->with('error', 'Error al verificar el pago: ' . $e->getMessage());
    }
}

    public function cancel()
    {
        return view('checkout.cancel');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // Manejar eventos de Stripe
        switch ($event->type) {
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->handleSubscriptionUpdate($subscription);
                break;
            
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $this->handlePaymentFailed($invoice);
                break;
        }

        return response()->json(['status' => 'success']);
    }


   public function manage()
{
    $user = auth()->user();
    $subscription = $user->subscription;
    
    // Obtener facturas de Stripe si tiene suscripción
    $invoices = [];
    if ($subscription && $subscription->stripe_customer_id) {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            // 🔍 DEBUG: Ver qué customer_id estamos usando
            \Log::info('Buscando facturas para customer: ' . $subscription->stripe_customer_id);
            
            $stripeInvoices = \Stripe\Invoice::all([
                'customer' => $subscription->stripe_customer_id,
                'limit' => 12,
            ]);
            
            // 🔍 DEBUG: Ver cuántas facturas encontró
            \Log::info('Facturas encontradas: ' . count($stripeInvoices->data));
            \Log::info('Facturas data: ' . json_encode($stripeInvoices->data));
            
            $invoices = $stripeInvoices->data;
        } catch (\Exception $e) {
            \Log::error('Error obteniendo facturas: ' . $e->getMessage());
        }
    } else {
        \Log::warning('No hay subscription o customer_id para user: ' . $user->id);
    }
    
    return view('subscription.manage', compact('user', 'subscription', 'invoices'));
}

public function downloadInvoice($invoiceId)
{
    $user = auth()->user();
    $subscription = $user->subscription;

    if (!$subscription) {
        return back()->with('error', 'No tienes acceso a esta factura');
    }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $invoice = \Stripe\Invoice::retrieve($invoiceId);

        // Verificar que la factura pertenece al usuario
        if ($invoice->customer !== $subscription->stripe_customer_id) {
            return back()->with('error', 'No tienes acceso a esta factura');
        }

        // Redirigir al PDF de Stripe
        return redirect($invoice->invoice_pdf);
    } catch (\Exception $e) {
        \Log::error('Error descargando factura: ' . $e->getMessage());
        return back()->with('error', 'Error al descargar la factura');
    }
}

public function cancelSubscription()
{
    $user = auth()->user();
    $subscription = $user->subscription;

    if (!$subscription || !$subscription->stripe_subscription_id) {
        return back()->with('error', 'No tienes una suscripción activa');
    }

    if ($subscription->canceled_at) {
        return back()->with('error', 'Tu suscripción ya está cancelada');
    }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        
        // Cancelar al final del período (no inmediatamente)
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
        $stripeSubscription->cancel_at_period_end = true;
        $stripeSubscription->save();

        // Actualizar en base de datos
        $subscription->update([
            'canceled_at' => now(),
            'ends_at' => now()->createFromTimestamp($stripeSubscription->current_period_end),
        ]);

        return back()->with('success', '✓ Suscripción cancelada. Podrás seguir usando tu plan hasta el ' . $subscription->ends_at->format('d/m/Y'));
    } catch (\Exception $e) {
        \Log::error('Error al cancelar suscripción: ' . $e->getMessage());
        return back()->with('error', 'Error al cancelar la suscripción');
    }
}

public function resumeSubscription()
{
    $user = auth()->user();
    $subscription = $user->subscription;

    if (!$subscription || !$subscription->stripe_subscription_id) {
        return back()->with('error', 'No tienes una suscripción');
    }

    if (!$subscription->canceled_at) {
        return back()->with('error', 'Tu suscripción no está cancelada');
    }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        
        // Reactivar suscripción
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
        $stripeSubscription->cancel_at_period_end = false;
        $stripeSubscription->save();

        // Actualizar en base de datos
        $subscription->update([
            'canceled_at' => null,
            'ends_at' => null,
        ]);

        return back()->with('success', '✓ Suscripción reactivada correctamente');
    } catch (\Exception $e) {
        \Log::error('Error al reactivar suscripción: ' . $e->getMessage());
        return back()->with('error', 'Error al reactivar la suscripción');
    }
}

public function changePlan(Request $request)
{
    $newPlan = $request->plan;
    $user = auth()->user();
    $subscription = $user->subscription;

    $prices = [
        'starter' => env('STRIPE_PRICE_STARTER'),
        'pro' => env('STRIPE_PRICE_PRO'),
    ];

    if (!isset($prices[$newPlan])) {
        return back()->with('error', 'Plan no válido');
    }

    if ($subscription && $subscription->plan === $newPlan) {
        return back()->with('info', 'Ya tienes el plan ' . ucfirst($newPlan));
    }

    if (!$subscription || !$subscription->stripe_subscription_id || $subscription->status !== 'active') {
        return redirect()->route('checkout', ['plan' => $newPlan]);
    }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
        
        $currentPriceAmount = $subscription->plan === 'starter' ? 900 : 2900;
        $newPriceAmount = $newPlan === 'starter' ? 900 : 2900;
        $isUpgrade = $newPriceAmount > $currentPriceAmount;
        
        \Stripe\Subscription::update($subscription->stripe_subscription_id, [
            'items' => [
                [
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $prices[$newPlan],
                ]
            ],
            'proration_behavior' => 'always_invoice',
        ]);

        // ✅ Actualizar en tabla subscriptions
        $subscription->update([
            'plan' => $newPlan,
            'stripe_price_id' => $prices[$newPlan],
        ]);

        // ✅ Actualizar en tabla users
        $user->update([
            'plan' => $newPlan,
        ]);

        $message = $isUpgrade ? 
            '✓ ¡Actualizado a ' . ucfirst($newPlan) . '! Ya puedes disfrutar de todas las ventajas' : 
            '✓ Plan cambiado a ' . ucfirst($newPlan) . ' correctamente';

        return back()->with('success', $message);
    } catch (\Exception $e) {
        \Log::error('Error cambiando plan: ' . $e->getMessage());
        return back()->with('error', 'Error al cambiar el plan: ' . $e->getMessage());
    }
}

    private function handleSubscriptionUpdate($subscription)
    {
        $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();
        
        if (!$user) return;

        if ($subscription->status === 'active') {
            $user->update([
                'subscription_ends_at' => now()->addMonth(),
            ]);
        } elseif ($subscription->status === 'canceled') {
            $user->update([
                'plan' => 'free',
                'subscription_ends_at' => null,
                'stripe_subscription_id' => null,
            ]);
        }

         \App\Models\Subscription::updateOrCreate(
        ['user_id' => $user->id],
        [
            'stripe_subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'ends_at' => $subscription->status === 'canceled' 
                ? now()->createFromTimestamp($subscription->current_period_end)
                : null,
            'canceled_at' => $subscription->cancel_at_period_end ? now() : null,
        ]
    );
    }

    private function handlePaymentFailed($invoice)
    {
        $user = \App\Models\User::where('stripe_customer_id', $invoice->customer)->first();
        
        if ($user) {
            // Aquí puedes enviar un email de notificación al usuario
            // Mail::to($user)->send(new PaymentFailedMail());
        }
    }

   
}