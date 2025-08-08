<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
{
    if ($request->routeIs('login') || $request->routeIs('login.check')) {
        return $next($request);
    }

    if (!session()->has('userId')) {
        return redirect()->route('login')->with('error', 'Unauthorized');
    }

    $role = decryptor('decrypt', session('roleIdentitiy'));

    if ($role !== 'admin') {
        return redirect()->route('login')->with('error', 'Only admins can access this page.');
    }

    return $next($request);
}

}
