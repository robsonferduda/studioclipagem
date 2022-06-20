<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TypeMessage extends Enum
{
    const FB_POSTS = 1;
    const FB_COMMENT = 2;
    const TWEETS = 3;
    const IG_POSTS = 4;
    const IG_COMMENT = 5;
    const FB_PAGE_POST = 7;
    const FB_PAGE_POST_COMMENT = 8;
}