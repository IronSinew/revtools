<?php

namespace App\Enums\Attributes;

use App\Interfaces\HasLabel;
use App\Traits\Labels\DefaultLabelsFromEnum;

enum EnumAttributeCommonKeys: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case IsPublic = 'is_public';
    case Description = 'description';
    case Group = 'group';
    case Label = 'label';
}
