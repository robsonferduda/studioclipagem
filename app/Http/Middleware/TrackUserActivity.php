<?php

namespace App\Http\Middleware;

use Closure;
use App\Audits;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Facades\Agent;
use OwenIt\Auditing\Models\Audit;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Ignora requisições para rotas públicas
        if ($request->is('login', 'register', 'assets/*')) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $user->update(['last_active_at' => Carbon::now()]); // Atualiza a última atividade

            // Registra um log detalhado na tabela audits
            Audits::create([
                'user_type' => get_class($user),
                'user_id' => $user->id,
                'event' => 'activity',
                'auditable_type' => null,
                'auditable_id' => null,
                'old_values' => null,
                'new_values' => json_encode($request->all()), // Dados da requisição
                'url' => Request::fullUrl(),
                'ip_address' => Request::ip(),
                'user_agent' => Agent::browser() . ' ' . Agent::version(Agent::browser()) . ' on ' . Agent::platform(),
                'tags' => 'user-activity',
                'created_at' => now(),
            ]);
        }

        return $next($request);
    }
}
