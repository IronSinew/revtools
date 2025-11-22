<?php

namespace App\Models;

use App\Enums\Mobs\MobTier;
use App\Enums\Mobs\MobType;
use App\Enums\SearchableType;
use App\Models\Pivots\ItemMob;
use App\Models\Pivots\MobRoom;
use App\ValueObjects\Mobs\MobDeprecatedData;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperMob
 */
class Mob extends BaseModel
{
    use HasSlug;
    use Searchable;

    protected $guarded = [];

    public function searchableAs(): string
    {
        return BaseModel::GlobalSearchIndex;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => self::class.$this->id,
            'model_id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'type' => SearchableType::Mob->value,
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'external_id' => 'integer',
            'type' => MobType::class,
            'tier' => MobTier::class,
            'level' => 'integer',
            'speed' => 'integer',
            'drops' => 'array',
            'deprecated_data' => MobDeprecatedData::class,
        ];
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->using(ItemMob::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class)
            ->using(MobRoom::class);
    }
}
