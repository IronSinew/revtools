<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperItemQuest
 */
class ItemQuest extends Pivot
{
    public $incrementing = true;
}
