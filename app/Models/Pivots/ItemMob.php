<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperItemMob
 */
class ItemMob extends Pivot
{
    public $incrementing = true;
}
