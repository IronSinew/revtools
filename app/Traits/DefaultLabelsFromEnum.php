<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait DefaultLabelsFromEnum
{
    /** @codeCoverageIgnore  */
    public function label(): string
    {
        return match ($this) {
            default => Str::headline($this->name)
        };
    }
}
