<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Tiket;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {


        $user = Auth::user();

        // Pastikan login
        if (!$user) {
            return redirect()->route('login');
        }

        // Pastikan role teknisi
         if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'Akses ditolak');
        }

        // Ambil tiket id dari route
        $tiketId = $request->route('id'); // asumsikan route: /tiket/{id}

        // Cari tiket
        $tiket = Tiket::findOrFail($tiketId);

        // Cek apakah teknisi yang login memang terdaftar di tiket ini
        $isAssigned = $tiket->teknisis()->where('users.id', $user->id)->exists();

        if (!$isAssigned && $role === 'teknisi') {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}
