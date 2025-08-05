<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'clientes';
    protected $fillable = ['nome',
                        'fl_ativo',
                        'emails',
                        'logo',
                        'logo_expandida',
                        'fl_audiencia',
                        'cod_unico',
                        'fl_print',
                        'fl_area_restrita',
                        'fl_areas',
                        'fl_sentimento',
                        'fl_link_relatorio',
                        'fl_radio',
                        'fl_web',
                        'fl_impresso',
                        'fl_texto_logo',
                        'fl_tv',
                        'fl_sentimento_cli',
                        'fl_relatorio_completo',
                        'fl_relatorio_consolidado',
                        'fl_retorno_midia'];

    public function area()
    {
        return $this->belongsToMany(Area::class,'noticia_cliente','cliente_id','area')->where('tipo_id', 1);
    }

    public function areas()
    {
        return $this->hasMany(ClienteArea::class, 'cliente_id', 'id');
    }

    public function usuario()
    {
        return $this->hasOne('App\User', 'client_id', 'id');
    }

    public function usuarios()
    {
        return $this->hasMany('App\User', 'client_id', 'id');
    }
}
