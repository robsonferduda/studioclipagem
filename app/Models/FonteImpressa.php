<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FonteImpressa extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'jornal_online';

    protected $fillable = ['codigo', 
                            'nome',
                            'cd_pais',
                            'cd_estado', 
                            'cd_cidade', 
                            'tipo', 
                            'coleta',
                            'modelo', 
                            'url', 
                            'with_login',
                            'valor_cm_capa_semana',
                            'valor_cm_capa_fim_semana',
                            'valor_cm_contracapa',
                            'valor_cm_demais_semana',
                            'valor_cm_demais_fim_semana',
                            'fl_ativo'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cd_cidade', 'cd_cidade');
    }

    public function tipoImpresso()
    {
        return $this->hasOne(TipoImpresso::class, 'id', 'tipo');
    }

    public function tipoColeta()
    {
        return $this->hasOne(TipoColeta::class, 'id', 'coleta');
    }

    public function edicoes()
    {
        return $this->hasMany(EdicaoJornalImpresso::class);
    }

    public function secoes()
    {
        return $this->hasMany(SecaoImpresso::class,'id_jornal_online','id');
    }

    public function getTotais($dt_inicio, $dt_fim)
    {
        $sql = "SELECT created_at::date, count(*) AS total 
                FROM pagina_edicao_jornal_online 
                WHERE deleted_at IS NULL 
                AND created_at between '$dt_inicio' AND '$dt_fim'
                GROUP BY created_at::date
                ORDER BY created_at ";

        return DB::select($sql);
    }
}