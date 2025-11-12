<?php

namespace App\Enums\Attributes;

use App\Interfaces\HasLabel;
use App\Traits\Labels\DefaultLabelsFromEnum;

enum EnumAttributeType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    // Use Callback if the enum should execute a callback to yield the value
    case Callback = 'callback';

    // Use Value if a primitive will be provided
    case Value = 'value';
}
