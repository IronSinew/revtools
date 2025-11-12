<?php

namespace App\Models;

use App\Enums\Items\ItemSlot;
use App\Enums\Items\ItemSubType;
use App\Enums\Items\ItemType;
use App\Enums\SearchableType;
use App\ValueObjects\Items\ItemDeprecatedData;
use App\ValueObjects\Items\ItemEffect;
use App\ValueObjects\Items\ItemRequirements;
use Laravel\Scout\Searchable;
use Spatie\LaravelData\DataCollection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperItem
 */
class Item extends BaseModel
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
        $effectDescriptions = null;
        if ($this->effects) {
            $effects = $this->effects->first()->toArray();
            $effectDescriptions = implode(' ', $effects['descriptions']);
        }

        return [
            'id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'type' => SearchableType::Item->value,
            'slug' => $this->slug,
            'name' => $this->name,
            'item_effects' => $this->effects ? $effectDescriptions : '',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'external_id' => 'integer',
            'type' => ItemType::class,
            'type_value' => 'integer',
            'sub_type' => ItemSubType::class,
            'slot' => ItemSlot::class,
            'gold_value' => 'integer',
            'speed' => 'integer',
            'effects' => DataCollection::class.':'.ItemEffect::class,
            'deprecated_data' => ItemDeprecatedData::class,
            'requirements' => ItemRequirements::class,
        ];
    }
}
