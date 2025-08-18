<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Student; // custom
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // правильно подключаем фасад
use App\Models\Permission; // custom

class checkStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверка наличия userId в сессии
        if (!Session::has('userId') || Session::get('userId') == null) {
            return redirect()->route('studentlogOut', ['locale' => app()->getLocale()]);
        }

        // Проверка, что студент с этим ID существует и активен (status = 1)
        $userExists = Student::where('id', currentUserId())->where('status', 1)->exists();

        if (!$userExists) {
            return redirect()->route('studentlogOut', ['locale' => app()->getLocale()]);
        }

        return $next($request);
    }
}
