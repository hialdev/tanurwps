<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;

class TanurController
{
    protected $baseUrl;
    protected $appId;

    public function __construct()
    {
        $this->baseUrl = config('al.apiwps.api_url');
        $this->appId = config('al.apiwps.app_id');
    }

    public function getAgentList($limit = 10, $offset = 0, $id_agent = null)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/agent/info/list", [
            'appid' => $this->appId,
            'limit' => $limit,
            'offset' => $offset,
            'id_agent' => $id_agent,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => false,
            'message' => 'Failed to fetch agent list',
            'error' => $response->body(),
        ];
    }

    public function getAgentDetail($id_agent)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/agent/info/detail", [
            'appid' => $this->appId,
            'id_agent' => $id_agent,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => false,
            'message' => 'Failed to fetch agent detail',
            'error' => $response->body(),
        ];
    }

    public function getAgentSuperiors($id_agent)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/agent/info/superiors", [
            'appid' => $this->appId,
            'id_agent' => $id_agent,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => false,
            'message' => 'Failed to fetch agent superiors',
            'error' => $response->body(),
        ];
    }

    public function notify($id_agent, $email = 1, $whatsapp = 1, $pushnotification = 1, $subject, $message, $forAgent = null)
    {
        $response = Http::asForm()->post("{$this->baseUrl}/support/notification/send", [
            'appid' => $this->appId,
            'id_agent' => $id_agent,
            'email' => $email,
            'whatsapp' => $whatsapp,
            'pushnotification' => $pushnotification,
            'subject' => $subject,
            'message' => $message,
            'template' => $forAgent != null ? ($forAgent == 1 ? 'informasi_agent' : 'informasi') : null,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => false,
            'message' => 'Failed to notify',
            'error' => $response->body(),
        ];
    }
}