<?php

namespace App\Http\Controllers;

use App\Services\UsageService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(UsageService $usageService)
    {
        $user = auth()->user();

        $stats = $usageService->getUserStats($user);

        return view('dashboard', compact('stats'));
    }
}
