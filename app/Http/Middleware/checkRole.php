<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User; //custom
use Illuminate\Http\Request;
use Session; //custom
use App\Models\Permission; //custom

class checkRole
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

    // Если полный доступ разрешён
    if ($user->full_access == "1") {
        return $next($request);
    }

    // Разрешённые методы без проверки прав
    $autoAccept = ['POST', 'PUT'];
    $method = $request->method(); // Пример: "POST"

    if (in_array($method, $autoAccept)) {
        return $next($request);
    }

    // Проверка на наличие прав доступа
    if (Permission::where('role_id', $user->role_id)
        ->where('name', $request->route()->getName())
        ->exists()) {
        return $next($request);
    }

    \Toastr::warning("You don't have permission to access this page");
    return redirect()->back();
}

}
