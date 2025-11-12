<?php

namespace App\Enums;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum SearchableType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Item = 'item';
}
