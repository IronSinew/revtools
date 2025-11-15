<?php

namespace App\Models;

use App\Enums\ClassType;
use App\Enums\Quests\QuestRewardType;
use App\Enums\SearchableType;
use App\Models\Pivots\ItemQuest;
use App\ValueObjects\Quests\QuestReward;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;
use Spatie\LaravelData\DataCollection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperQuest
 */
class Quest extends BaseModel
{
    use HasSlug;
    use Searchable;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'required_class' => AsEnumCollection::of(ClassType::class),
            'raw_rewards' => DataCollection::class.':'.QuestReward::class,
            'objectives' => 'array',
            'steps' => 'array',
            'quest_chain' => 'array',
            'reward_types' => AsEnumCollection::of(QuestRewardType::class),
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

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->using(ItemQuest::class);
    }

    public function mob(): BelongsTo
    {
        return $this->belongsTo(Mob::class);
    }
}
