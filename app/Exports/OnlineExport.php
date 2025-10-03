<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OnlineExport implements FromView
{
    public $online;
    public $recentActivities;
    public $dt_inicial;
    public $dt_final;
    public $usuario;

    public function __construct($online, $recentActivities, $dt_inicial = null, $dt_final = null, $usuario = null)
    {
        $this->online = $online;
        $this->recentActivities = $recentActivities;
        $this->dt_inicial = $dt_inicial;
        $this->dt_final = $dt_final;
        $this->usuario = $usuario;
    }

    public function view(): View
    {
        return view('usuarios.online_excel', [
            'online' => $this->online,
            'recentActivities' => $this->recentActivities,
            'dt_inicial' => $this->dt_inicial,
            'dt_final' => $this->dt_final,
            'usuario' => $this->usuario,
        ]);
    }
}