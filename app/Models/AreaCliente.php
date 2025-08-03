<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AreaCliente extends Model implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $connection = 'pgsql';
    protected $table = 'area_cliente';

    protected $fillable = ['ordem','expressao','ativo'];

}