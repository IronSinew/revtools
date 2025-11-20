<?php

namespace App\Models;

use App\Enums\Regions\RoomExitType;
use App\Enums\SearchableType;
use App\ValueObjects\Regions\RegionPosition;
use App\ValueObjects\Regions\RoomPosition;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperRegion
 */
class Region extends BaseModel
{
    use HasSlug;
    use Searchable;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'coordinates' => RegionPosition::class,
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function searchableAs(): string
    {
        return BaseModel::GlobalSearchIndex;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'type' => SearchableType::Quest->value,
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
