<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Area extends Model implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $connection = 'pgsql';
    protected $table = 'area';

    protected $fillable = ['descricao'];

    public function clienteArea()
    {
        return $this->belongsTo(ClienteArea::class, 'id', 'area_id');
    }
}