<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperMobRegion
 */
class MobRegion extends Pivot
{
    public $incrementing = true;
}
