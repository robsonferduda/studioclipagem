<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SocialMedia extends Enum
{
    const FACEBOOK = 1;
    const INSTAGRAM = 2;
    const TWITTER = 3;
}
