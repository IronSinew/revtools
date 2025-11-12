<?php

namespace App\Enums;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum UserRoleEnum: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case MEMBER = '';
    case ADMIN = 'admin';
    case CONTRIBUTOR = 'contributor';
}
