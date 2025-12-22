<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'es'])) {
            abort(400);
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back
        return redirect()->back();
    }
}
