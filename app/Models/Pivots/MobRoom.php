<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperMobRoom
 */
class MobRoom extends Pivot
{
    public $incrementing = true;
}
