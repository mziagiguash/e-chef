<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User; //added
use Session; //added

class isSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    if (!Session::has('userId') || !Session::has('roleIdentity')) {
        return redirect()->route('logOut');
    }

    $user = User::find(currentUserId());

    if (!$user) {
        return redirect()->route('logOut');
    }

    app()->setLocale($user->language);

    if (currentUser() !== 'superadmin') {
        return redirect()->back()->with('danger', 'Access Denied');
    }

    return $next($request);
}

}
