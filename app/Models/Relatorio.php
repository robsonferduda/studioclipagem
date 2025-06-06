<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $table = 'relatorios';

    protected $fillable = [
        'id_tipo',
        'ds_nome',
        'cd_usuario',
        'dt_requisicao',
        'dt_finalizacao',
        'total_view',
        'situacao'
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'cd_usuario');
    }
}