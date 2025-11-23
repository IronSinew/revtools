<?php

namespace App\Models;

use App\Enums\Regions\RoomExitType;
use App\Models\Pivots\ItemRoom;
use App\Models\Pivots\MobRoom;
use App\ValueObjects\Regions\RoomPosition;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mavinoo\Batch\Traits\HasBatch;

/**
 * @mixin IdeHelperRoom
 */
class Room extends BaseModel
{
    use HasBatch;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'coordinates' => RoomPosition::class,
            'exits' => AsEnumCollection::of(RoomExitType::class),
            'exit_region_direction' => RoomExitType::class,
            'npcs' => 'array',
        ];
    }

    public function mobs(): BelongsToMany
    {
        return $this->belongsToMany(Mob::class)
            ->orderBy('level', 'desc')
            ->using(MobRoom::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->orderBy('type', 'desc')
            ->orderBy('effective_required_level', 'desc')
            ->using(ItemRoom::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function exitRegion(): belongsTo
    {
        return $this->belongsTo(Region::class, 'exit_region_id');
    }
}
