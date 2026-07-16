<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class RoleMiddleware {
    public function handle(Request $request, Closure $next, string ...$roles): mixed {
        $user = $request->user();
        if (!$user) return redirect()->route('login');
        if (empty($roles)) return $next($request);
        if ($user->role === 'superadmin' && !in_array('student', $roles)) return $next($request);
        if (in_array($user->role, $roles)) return $next($request);
        return match($user->role) {
            'superadmin'       => redirect()->route('superadmin.dashboard')->with('error','Access denied.'),
            'admin','officer'  => redirect()->route('admin.dashboard')->with('error','Access denied.'),
            'student'          => redirect()->route('student.dashboard')->with('error','Access denied.'),
            default            => redirect()->route('login'),
        };
    }
}
