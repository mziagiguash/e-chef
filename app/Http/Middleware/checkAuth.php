<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User; //custom
use Illuminate\Http\Request;
use Session; //custom
use App\Models\Permission; //custom

class checkAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
{
    if (!Session::has('userId') || Session::get('userId') === null) {
        return redirect()->route('logOut', ['locale' => app()->getLocale()]);
    }

    $user = User::where('status', 1)
                ->where('id', currentUserId())
                ->first();

    if (!$user) {
        return redirect()->route('logOut', ['locale' => app()->getLocale()]);
    }

    return $next($request);
}

}
