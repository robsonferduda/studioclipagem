<?php

namespace App\Listeners;

use App\Audits;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Facades\Agent;

class LogUserLogout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $userIp = Request::ip();
        $browser = Agent::browser(); // Nome do navegador (ex.: Chrome)
        $browserVersion = Agent::version($browser); // Versão do navegador
        $platform = Agent::platform(); // Sistema operacional (ex.: Windows, macOS)
        $device = Agent::device(); // Dispositivo (ex.: iPhone, Desktop)

        Audits::create([
            'user_id' => $event->user->id,
            'event' => 'logout',
            'auditable_type' => 'App\User',
            'user_type' => 'App\User',
            'ip_address' => $userIp,
            'user_agent' => Request::header('User-Agent')
        ]);
    }
}
