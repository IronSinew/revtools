<?php

namespace App\Models;

use App\Enums\ClassType;
use App\ValueObjects\Quests\QuestReward;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelData\DataCollection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Quest extends BaseModel
{
    use HasSlug;

    public function previousQuests(): BelongsToMany
    {
        return $this->belongsToMany(
            Quest::class,
            'quest_prerequisites',
            'quest_id',
            'prerequisite_id'
        );
    }

    protected function casts(): array
    {
        return [
            'required_class' => AsEnumCollection::of(ClassType::class),
            'rewards' => DataCollection::class.':'.QuestReward::class,
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'level'])
            ->saveSlugsTo('slug');
    }
}
