<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TypeRule extends Enum
{
    const TODAS = 1;
    const ALGUMAS = 2;
    const NENHUMA = 3;
}
