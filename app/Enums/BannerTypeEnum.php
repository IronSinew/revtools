<?php

namespace App\Enums;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum BannerTypeEnum: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case success = 'success';
    case danger = 'error';
    case warning = 'warn';
    case info = 'info';
}
