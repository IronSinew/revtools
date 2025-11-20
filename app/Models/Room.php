<?php

namespace App\Models;

use App\Enums\Regions\RoomColor;
use App\Enums\Regions\RoomExitType;
use App\ValueObjects\Regions\RoomPosition;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRoom
 */
class Room extends BaseModel
{

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'coordinates' => RoomPosition::class,
            'exits' => AsEnumCollection::of(RoomExitType::class),
            'exit_region_direction' => RoomExitType::class,
        ];
    }
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
