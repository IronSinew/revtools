<?php

namespace App\Traits\Labels;

use Illuminate\Support\Str;

trait DefaultLabelsFromEnum
{
    /** @codeCoverageIgnore  */
    // @phpstan-ignore-next-line
    public function label(): string
    {
        return match ($this) {
            default => Str::headline($this->name)
        };
    }
}
