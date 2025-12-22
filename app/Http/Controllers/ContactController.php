<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Enviar el email
        $emailContent = "Nombre: " . $validated['name'] . "\n";
        $emailContent .= "Email: " . $validated['email'] . "\n\n";
        $emailContent .= "Mensaje:\n" . $validated['message'];
        
        Mail::raw($emailContent, function ($message) use ($validated) {
            $message->to('jorgeherrero.dev@gmail.com')
                ->subject('Contacto desde Reunai: ' . $validated['subject'])
                ->replyTo($validated['email'], $validated['name']);
        });
        
        return redirect('/')->with('success', 'Mensaje enviado correctamente');
    }
}