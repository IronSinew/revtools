<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperItemRoom
 */
class ItemRoom extends Pivot
{
    public $incrementing = true;
}
