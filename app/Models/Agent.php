<?php

namespace App\Models;

class Agent
{
    protected $tanurApi;

    public function __construct()
    {
        $this->tanurApi = new \App\Http\Controllers\Api\TanurController();
    }

    public function find($agentId)
    {
        $response = $this->tanurApi->getAgentDetail($agentId);
        return $response;
    }

    public function getId()
    {
        return session('agent_id');
    }

    public function all($limit = 1000, $offset = 0)
    {
        $response = $this->tanurApi->getAgentList($limit, $offset);
        return $response;
    }

    public function superiors($agentId)
    {
        $response = $this->tanurApi->getAgentSuperiors($agentId);
        return $response;
    }

    public static function totalScore($agentId){
        $workspaces = Workspace::where('agent_id', $agentId)->get();
        $totalScore = 0;
        foreach ($workspaces as $workspace){
            $totalScore += $workspace->live_score;
        }

        return $totalScore;
    }
}
