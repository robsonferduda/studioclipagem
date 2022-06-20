<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FbReaction extends Enum
{
    const LIKE = 1;
    const LOVE = 2;
    const WOW = 3;
    const HAHA = 4;
    const SAD = 5;
    const ANGRY = 6;
    const THANKFUL = 7;
}