<script setup>
import Fieldset from "primevue/fieldset";
import { computed, onMounted, ref } from "vue";

import ItemType from "@/Composables/GeneratedEnumObjects/Items-ItemType.json";

const props = defineProps({
    data: {
        type: [Array, Object],
        default: () => {},
    },
    forceDisplay: {
        type: Boolean,
        default: () => false,
    },
    titleOnly: {
        type: Boolean,
        default: () => false,
    },
});

const tooltipRef = ref(null);
const tippyTarget = ref(null);

const tooltipHtml = computed(() => {
    if (tooltipRef.value && !props.titleOnly) {
        {
            return tooltipRef.value.innerHTML;
        }
    }

    return null;
});
</script>

<template>
    <div
        ref="tippyTarget"
        v-tippy="{
            content: tooltipHtml,
            followCursor: true,
            allowHTML: true,
            interactive: true,
            duration: 0,
            offset: [10, 10],
            appendTo: 'parent',
            placement: 'bottom-start',
        }"
        style="width: auto"
        :class="{ hidden: forceDisplay }"
    >
        <span
            :class="{
                q4: data.type === ItemType.Weapon.value,
                q3: data.type === ItemType.Armor.value,
            }"
        >
            [{{ data.name }}]
        </span>
    </div>
    <div ref="tooltipRef" :class="{ hidden: !forceDisplay }">
        <div class="item-tooltip p-3 border rounded-md">
            <b
                :class="{
                    q4: data.type === ItemType.Weapon.value,
                    q3: data.type === ItemType.Armor.value,
                }"
            >
                {{ data.name }}
            </b>

            <div class="grid grid-cols-2 grid-rows-1 gap-2 mt-2">
                <div class="capitalize">{{ data.type }}</div>
                <div class="text-right q1 capitalize">
                    {{ (data.sub_type || "").replace("_", " ") }}
                </div>
            </div>

            <div v-if="data.type === 'armor'">
                <div class="grid grid-cols-2 grid-rows-1 gap-2">
                    <div class="q">
                        <span>{{ data.type_value }} Armor</span>
                    </div>
                    <div class="text-right capitalize">{{ data.slot }}</div>
                </div>
            </div>
            <div v-if="data.type === 'weapon'">
                <div class="grid grid-cols-2 grid-rows-1 gap-2">
                    <div class="q">{{ data.type_value }} Damage</div>
                    <div class="text-right">Speed {{ data.speed }}</div>
                </div>
            </div>

            <Fieldset
                v-if="data.requirements"
                legend="Requirements"
                class="mt-4 q1"
            >
                <div v-if="data.requirements?.classes?.length">
                    Limited To:
                    <span
                        v-for="(className, idx) in data.requirements.classes"
                        :key="`usable-class-${idx}`"
                        class="capitalize"
                    >
                        {{ idx > 0 ? ", " : "" }}{{ className }}
                    </span>
                </div>

                <div v-if="data.requirements?.aa_level">
                    AA Level: {{ data.requirements.aa_level }}
                </div>
                <div v-else-if="data.requirements?.level">
                    Level: {{ data.requirements.level }}
                </div>
                <div v-if="data.requirements?.skill">
                    Skill: {{ data.requirements.skill.replace("_", " ") }}
                    {{ data.requirements.skill_level }}
                </div>
                <div v-if="data.requirements?.spell">
                    Spell: {{ data.requirements.spell.replace("_", " ") }}
                    {{ data.requirements.spell_level }}
                </div>
                <div
                    v-for="(requirement, key) in data.requirements || []"
                    :key="`requirement-${key}`"
                >
                    <div
                        v-if="
                            ![
                                'aa_level',
                                'speed',
                                'classes',
                                'skill',
                                'skill_level',
                                'spell',
                                'spell_level',
                            ].includes(key) && requirement
                        "
                    >
                        <span class="capitalize">{{ key }}:</span>
                        {{ requirement }}
                    </div>
                </div>
            </Fieldset>

            <Fieldset v-if="data.effects" legend="Effects" class="mt-4">
                <div v-if="data.effects?.length">
                    <template v-for="effect in data.effects">
                        <div
                            v-for="(desc, idx) in effect.descriptions"
                            :key="`description-${idx}`"
                            class="capitalize q2"
                        >
                            {{
                                (desc || "")
                                    .replace(["Item Effect: "], "")
                                    .replace("Item Casts ", "")
                                    .replaceAll("*", " ")
                            }}
                        </div>
                    </template>
                </div>
            </Fieldset>

            <div class="q text-right mt-4">
                Value: {{ data.gold_value.toLocaleString() }}
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.item-tooltip {
    box-sizing: border-box;
    width: 100%;
    max-width: 400px;
    min-width: 300px;
    overflow: visible !important;
    z-index: 5132413647;
    color: rgb(var(--surface-0));
    font-family:
        Verdana, "Open Sans", Arial, "Helvetica Neue", Helvetica, sans-serif;
    background-color: hsl(231deg 68.88% 12.47% / 80%);

    .tooltip-icon {
        height: 44px;
        left: -44px;
        margin: 0 !important;
        padding: 0;
        position: absolute;
        top: -1px;
        width: 44px;
    }

    fieldset {
        color: rgb(var(--surface-0));
        background-color: hsl(231deg 68.88% 12.47% / 70%) !important;

        legend > span {
            color: rgb(var(--surface-0)) !important;
        }
    }
}
</style>
