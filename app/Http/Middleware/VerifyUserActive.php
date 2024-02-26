<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendUrlEmailJob;
use Illuminate\Support\Facades\Log;

class VerifyUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->active == false) {
                // Si el usuario no está activo, envía un correo electrónico y muestra una vista
                Log::info('El usuario no está activo.'.$user);
                SendUrlEmailJob::dispatch($user)->onQueue('emailsUrl');
                return redirect()->route('mailView');
            }
            
            // Si el usuario está activo, permite que la solicitud continúe normalmente
            return $next($request);
        }
    
        // Si el usuario no está autenticado, redirige al inicio de sesión
        return redirect()->route('login');
    }
}
