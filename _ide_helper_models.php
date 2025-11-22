<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBaseModel {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $external_id
 * @property string $name
 * @property string $slug
 * @property \App\Enums\Items\ItemType $type
 * @property int|null $type_value
 * @property \App\Enums\Items\ItemSubType|null $sub_type
 * @property \App\Enums\Items\ItemSlot|null $slot
 * @property int $gold_value
 * @property int|null $speed
 * @property int $effective_required_level
 * @property \Spatie\LaravelData\Contracts\BaseData|\Spatie\LaravelData\Contracts\TransformableData|null $deprecated_data
 * @property \Spatie\LaravelData\Contracts\BaseData|\Spatie\LaravelData\Contracts\TransformableData|null $requirements
 * @property \Spatie\LaravelData\DataCollection|null $effects
 * @property string|null $description
 * @property string|null $discovered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Pivots\ItemQuest|\App\Models\Pivots\ItemMob|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quest> $quests
 * @property-read int|null $quests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDeprecatedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDiscoveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffectiveRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereGoldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereSlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereSubType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTypeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItem {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $external_id
 * @property string $name
 * @property string $slug
 * @property \App\Enums\Mobs\MobType $type
 * @property int $level
 * @property \App\Enums\Mobs\MobTier $tier
 * @property string|null $location
 * @property \Spatie\LaravelData\Contracts\BaseData|\Spatie\LaravelData\Contracts\TransformableData|null $deprecated_data
 * @property array<array-key, mixed>|null $drops
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pivots\MobRoom|\App\Models\Pivots\ItemMob|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDeprecatedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereDrops($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mob whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMob {}
}

namespace App\Models\Pivots{
/**
 * @property int $id
 * @property int $item_id
 * @property int $mob_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemMob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemMob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemMob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemMob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemMob whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemMob whereMobId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItemMob {}
}

namespace App\Models\Pivots{
/**
 * @property int $id
 * @property int $quest_id
 * @property int $item_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemQuest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemQuest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemQuest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemQuest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemQuest whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemQuest whereQuestId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItemQuest {}
}

namespace App\Models\Pivots{
/**
 * @property int $id
 * @property int $room_id
 * @property int $item_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemRoom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemRoom whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemRoom whereRoomId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperItemRoom {}
}

namespace App\Models\Pivots{
/**
 * @property int $id
 * @property int $mob_id
 * @property int $room_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MobRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MobRoom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MobRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MobRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MobRoom whereMobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MobRoom whereRoomId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMobRoom {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $external_id
 * @property string $name
 * @property string $slug
 * @property int $level
 * @property int $gold
 * @property \Illuminate\Support\Collection<int, \App\Enums\ClassType>|null $required_class
 * @property int|null $mob_id
 * @property int|null $previous_quest_id
 * @property array<array-key, mixed>|null $quest_chain
 * @property array<array-key, mixed> $objectives
 * @property array<array-key, mixed> $steps
 * @property \Spatie\LaravelData\DataCollection|null $raw_rewards
 * @property \Illuminate\Support\Collection<int, \App\Enums\Quests\QuestRewardType>|null $reward_types
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Pivots\ItemQuest|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Mob|null $mob
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereGold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereMobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereObjectives($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest wherePreviousQuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereQuestChain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereRawRewards($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereRequiredClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereRewardTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperQuest {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Spatie\LaravelData\Contracts\BaseData|\Spatie\LaravelData\Contracts\TransformableData|null $coordinates
 * @property string|null $color
 * @property array<array-key, mixed>|null $connections
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereConnections($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRegion {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $color_code
 * @property string $terrain_color
 * @property \Spatie\LaravelData\Contracts\BaseData|\Spatie\LaravelData\Contracts\TransformableData $coordinates
 * @property \Illuminate\Support\Collection<int, \App\Enums\Regions\RoomExitType>|null $exits
 * @property int|null $exit_region_id
 * @property \App\Enums\Regions\RoomExitType|null $exit_region_direction
 * @property array<array-key, mixed>|null $npcs
 * @property int $region_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Region|null $exitRegion
 * @property-read \App\Models\Pivots\MobRoom|\App\Models\Pivots\ItemRoom|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mob> $mobs
 * @property-read int|null $mobs_count
 * @property-read \App\Models\Region $region
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereExitRegionDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereExitRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereExits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereNpcs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereTerrainColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRoom {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property \App\Enums\UserRoleEnum $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

