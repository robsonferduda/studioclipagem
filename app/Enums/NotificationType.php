<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationType extends Enum
{
    const MENTION = 1;
    const ENGAJAMENTO = 2;
    const KEYWORDS = 3;
    const HASHTAG = 4;
}