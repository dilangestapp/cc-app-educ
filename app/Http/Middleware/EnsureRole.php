<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Usage:
     *  ->middleware('role:admin')
     *  ->middleware('role:eleve')
     *  ->middleware('role:enseignant')
     *  ->middleware('role:parent')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $type = strtolower((string) ($user->type_compte ?? 'eleve'));
        $roles = array_map(fn ($r) => strtolower(trim($r)), $roles);

        if (!in_array($type, $roles, true)) {
            // 🚫 Pas le bon rôle => on redirige vers son dashboard
            return redirect()->route($this->homeRouteName($type));
        }

        return $next($request);
    }

    private function homeRouteName(string $type): string
    {
        return match ($type) {
            'admin' => 'dashboard',
            'enseignant' => 'enseignant.dashboard',
            'parent' => 'parent.dashboard',
            default => 'eleve.dashboard',
        };
    }
}
