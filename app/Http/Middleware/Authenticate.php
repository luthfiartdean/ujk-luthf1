<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // ✅ GANTI — arahkan ke /login bukan /home
        return $request->expectsJson() ? null : '/login';
    }
}