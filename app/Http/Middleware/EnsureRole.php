<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $type = strtolower((string) ($user->type_compte ?? 'eleve'));
        $roles = array_map(fn ($r) => strtolower(trim($r)), $roles);

        if (!in_array($type, $roles, true)) {
            return redirect()->route($this->homeRouteName($user));
        }

        // ✅ élève sans classe => forcer choix classe
        if ($type === 'eleve' && empty($user->classe_id) && !$request->routeIs('eleve.classe.*')) {
            return redirect()->route('eleve.classe.edit');
        }

        return $next($request);
    }

    private function homeRouteName($user): string
    {
        $type = strtolower((string) ($user->type_compte ?? 'eleve'));

        if ($type === 'eleve' && empty($user->classe_id)) {
            return 'eleve.classe.edit';
        }

        return match ($type) {
            'admin' => 'dashboard',
            'enseignant' => 'enseignant.dashboard',
            'parent' => 'parent.dashboard',
            default => 'eleve.dashboard',
        };
    }
}
