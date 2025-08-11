<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaCliente extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'noticia_cliente';

    protected $fillable = ['cliente_id',
                            'tipo_id',
                            'noticia_id',
                            'monitoramento_id',
                            'sentimento',
                            'area',
                            'ordem',
                            'fl_boletim',
                            'fl_enviada',
                            'id_noticia_gerada',
                            'id_noticia_origem',
                            'misc_data'];

    protected $casts = [
        'misc_data' => 'array'
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function noticiaWeb()
    {
        return $this->hasOne(NoticiaWeb::class, 'id', 'noticia_id');
    }

    public function noticiaImpressa()
    {
        return $this->hasOne(JornalImpresso::class, 'id', 'noticia_id');
    }

    public function monitoramento()
    {
        return $this->belongsTo(Monitoramento::class, 'monitoramento_id', 'id');
    }

    /**
     * Obter tags da notícia
     */
    public function getTags()
    {
        return $this->misc_data['tags_noticia'] ?? [];
    }

    /**
     * Adicionar tag à notícia
     */
    public function addTag($tag)
    {
        $tags = $this->getTags();
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $miscData = $this->misc_data ?? [];
            $miscData['tags_noticia'] = $tags;
            $this->misc_data = $miscData;
            $this->save();
        }
        return true;
    }

    /**
     * Remover tag da notícia
     */
    public function removeTag($tag)
    {
        $tags = $this->getTags();
        $tags = array_filter($tags, function($t) use ($tag) {
            return $t !== $tag;
        });
        $miscData = $this->misc_data ?? [];
        $miscData['tags_noticia'] = array_values($tags);
        $this->misc_data = $miscData;
        $this->save();
        return true;
    }

    /**
     * Definir tags da notícia
     */
    public function setTags($tags)
    {
        $miscData = $this->misc_data ?? [];
        $miscData['tags_noticia'] = array_values(array_unique($tags));
        $this->misc_data = $miscData;
        $this->save();
        return true;
    }
}