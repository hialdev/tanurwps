<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateAgentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah header tersedia (initial request dari Flutter)
        $headerAgentId = $request->header('Tanur-Agent-Id');
        $headerAgentRole = $request->header('Tanur-Agent-Role');

        // Gunakan session jika header tidak tersedia (navigasi lanjutan)
        $agentId = $headerAgentId ?? session('agent_id');
        $agentRole = $headerAgentRole ?? session('agent_role');

        if (!$agentId || !$agentRole) {
            return response()->json(['message' => 'Unauthorized: Missing Agent Info'], 401);
        }

        // Validasi agen dari database
        $tanurapi = new \App\Http\Controllers\Api\TanurController();
        $agent = $tanurapi->getAgentDetail($agentId);
        $agent = (object) $agent['data']['agent'] ?? null;
        
        if (!$agent) {
            return response()->json(['message' => 'Unauthorized: Agent not found'], 401);
        }

        // Simpan ke session jika berasal dari header (initial request)
        if ($headerAgentId && $headerAgentRole) {
            session([
                'agent_id' => $agentId,
                'agent_role' => $agentRole
            ]);
        }

        return $next($request);
    }
}
