<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\Items\ItemEffectType;
use App\Enums\Items\ItemSlot;
use App\Enums\Items\ItemSubType;
use App\Enums\Items\ItemType;
use App\Models\Item;
use App\ValueObjects\Items\ItemDeprecatedData;
use App\ValueObjects\Items\ItemEffect;
use App\ValueObjects\Items\ItemObject;
use App\ValueObjects\Items\ItemRequirements;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/Items.json');
        $now = now();

        foreach (JsonParser::parse($file) as $key => $rawItem) {
            $classTypes = [];
            foreach (array_filter(explode('&', $rawItem['RequiredClass'])) as $className) {
                $classTypes[] = ClassType::from(\Str::lower(trim($className)));
            }

            $effects = [];

            // Spell effects
            if (
                falseyToNull($rawItem['UsableSpell']) &&
                falseyToNull($rawItem['UsableSpellLevel']) &&
                falseyToNull($rawItem['UsableSpellRechargeTime'])
            ) {
                $effects[] = new ItemEffect(
                    type: ItemEffectType::Spell,
                    name: falseyToNull($rawItem['UsableSpell']),
                    level: falseyToNull($rawItem['UsableSpellLevel']),
                    recharge_time: falseyToNull($rawItem['UsableSpellRechargeTime']),
                    descriptions: ! empty($rawItem['ItemEffects']) && empty($effects) ? $rawItem['ItemEffects'] : null,
                );
            }

            // Proc effects
            if (
                falseyToNull($rawItem['ProcSpellName']) &&
                falseyToNull($rawItem['ProcTrigger']) &&
                falseyToNull($rawItem['ProcLevel']) &&
                falseyToNull($rawItem['ProcChance'])
            ) {
                $effects[] = new ItemEffect(
                    type: ItemEffectType::Proc,
                    name: falseyToNull($rawItem['ProcSpellName']),
                    level: falseyToNull($rawItem['ProcLevel']),
                    value: falseyToNull($rawItem['ProcChance']),
                    descriptions: ! empty($rawItem['ItemEffects']) && empty($effects) ? $rawItem['ItemEffects'] : null,
                    trigger: falseyToNull($rawItem['ProcTrigger']),
                );
            }

            // Potion effects
            elseif (
                falseyToNull($rawItem['PotionEffect']) &&
                falseyToNull($rawItem['PotionEffectValue'])
            ) {
                $effects[] = new ItemEffect(
                    type: ItemEffectType::Potion,
                    name: falseyToNull($rawItem['PotionEffect']),
                    value: falseyToNull($rawItem['PotionEffectValue']),
                    descriptions: ! empty($rawItem['ItemEffects']) && empty($effects) ? $rawItem['ItemEffects'] : null,
                );
            }

            // Poison effects
            elseif (falseyToNull($rawItem['PoisonEffect'])) {
                $effects[] = new ItemEffect(
                    type: ItemEffectType::Poison,
                    name: falseyToNull($rawItem['PoisonEffect']),
                    descriptions: ! empty($rawItem['ItemEffects']) && empty($effects) ? $rawItem['ItemEffects'] : null,
                );
            }

            // Scroll effects
            elseif (falseyToNull($rawItem['ScrollEffect'])) {
                $effects[] = new ItemEffect(
                    type: ItemEffectType::Scroll,
                    name: falseyToNull($rawItem['ScrollEffect']),
                    descriptions: ! empty($rawItem['ItemEffects']) && empty($effects) ? $rawItem['ItemEffects'] : null,
                );
            }

            // just other item descriptions
            elseif (falseyToNull($rawItem['ItemEffects'])) {
                $effects[] = new ItemEffect(
                    type: ItemEffectType::Generic,
                    descriptions: ! empty($rawItem['ItemEffects']) && empty($effects) ? $rawItem['ItemEffects'] : null,
                );
            }

            $itemType = ItemType::from(\Str::snake($rawItem['Type']));
            $typeValue = falseyToNull($rawItem['Damage']) ??
                falseyToNull($rawItem['Armor']) ??
                ($itemType !== ItemType::Usable ? 0 : null);

            $item = new ItemObject(
                external_id: $rawItem['Id'],
                name: $rawItem['Name'],
                gold_value: $rawItem['Value'],
                type: $itemType,
                sub_type: $this->funnelSubTypes(
                    $itemType,
                    falseyToNull($rawItem['Slot'], fn () => ItemSlot::from(\Str::snake($rawItem['Slot']))),
                    $rawItem['ArmorType'],
                    $rawItem['WeaponType'],
                    $this->filterShieldType($rawItem['ShieldType'])
                ),
                slot: falseyToNull(
                    $rawItem['Slot'] && $itemType !== ItemType::Usable,
                    fn () => ItemSlot::from(\Str::snake($rawItem['Slot']))
                ),
                speed: falseyToNull($rawItem['Level']),
                type_value: $typeValue,
                deprecated_data: new ItemDeprecatedData(
                    stamina: falseyToNull($rawItem['Stamina']),
                    duration: falseyToNull($rawItem['Duration']),
                    weight: falseyToNull($rawItem['Weight']),
                ),
                requirements: new ItemRequirements(
                    level: falseyToNull($rawItem['RequiredLevel']),
                    classes: falseyToNull(! empty($classTypes) ? $classTypes : null),
                    skill: falseyToNull($rawItem['RequiredSkill']),
                    skill_level: falseyToNull($rawItem['RequiredSkillLevel']),
                    quest: falseyToNull($rawItem['RequiredQuest']),
                    strength: falseyToNull($rawItem['RequiredStr']),
                    agility: falseyToNull($rawItem['RequiredAgi']),
                    intelligence: falseyToNull($rawItem['RequiredInt']),
                    focus: falseyToNull($rawItem['RequiredFoc']),
                    toughness: falseyToNull($rawItem['RequiredTog']),
                    charisma: falseyToNull($rawItem['RequiredChar']),
                    aa_level: falseyToNull($rawItem['RequiredAALevel']),
                    spell: falseyToNull($rawItem['RequiredSpell']),
                    spell_level: falseyToNull($rawItem['RequiredSpellLevel']),
                ),
                effects: ! empty($effects) ? $effects : null,
                description: falseyToNull($rawItem['Description']),
            );
            //            if (!empty($effects)){
            //                xdebug_break();
            //            }

            // TODO: parse DroppedBy and attempt to match to mobs
            // TODO(blocked): Need to parse Mobs.json

            // TODO: parse Location and attempt to match to location
            // TODO(blocked): Need to parse WorldData.json

            $arrayData = collect($item->toArray())
                ->transform(function (mixed $value) {
                    if (is_array($value)) {
                        $filtered = array_filter($value);

                        return ! empty($filtered) ? json_encode($filtered) : null;
                    }

                    return $value;
                })
                ->toArray();
            $arrayData['created_at'] = $now;
            $arrayData['updated_at'] = $now;
            $arrayData['slug'] = \Str::slug($arrayData['name']);
            $arrayData['effective_required_level'] = ($item->requirements->level ?? 0);
            if ($item->requirements?->aa_level) {
                $arrayData['effective_required_level'] = ($item->requirements->aa_level ?? 0) + 100;
            }

            $toInsert[] = $arrayData;

            if (count($toInsert) > 500) {
                Item::insert($toInsert);
                unset($toInsert);
                $now = now();
            }
        }

        if (! empty($toInsert)) {
            Item::insert($toInsert);
        }
    }

    private function funnelSubTypes(ItemType $itemType, ?ItemSlot $slotType, ?string ...$types): ?ItemSubType
    {
        $typesImploded = implode(', ', array_filter($types));

        if ($itemType === ItemType::Usable) {
            return match ($slotType) {
                ItemSlot::Potion => ItemSubType::Potion,
                ItemSlot::Poison => ItemSubType::Poison,
                ItemSlot::Scroll => ItemSubType::Scroll,
            };
        }

        if (! $typesImploded) {
            return null;
        }

        foreach ($types as $type) {
            $tryType = ItemSubType::tryFrom(\Str::snake($type));
            if ($tryType) {
                return $tryType;
            }
        }

        throw new \Exception("Could not find item subtype for: {$typesImploded}");
    }

    private function filterShieldType(string $type): ?string
    {
        return match ($type) {
            'Jewelry/Wand' => 'jewelry_wand',
            'Unknown Shield', '' => null,
            default => $type,
        };
    }
}
