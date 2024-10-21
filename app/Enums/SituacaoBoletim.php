<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SituacaoBoletim extends Enum
{
    const GERADO = 1;
    const ENVIADO = 2;
    const RECEBIDO = 3;
}