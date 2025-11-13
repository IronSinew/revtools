<script setup>
import { Link } from "@inertiajs/vue3";
import Card from "primevue/card";
import Fieldset from "primevue/fieldset";
import Message from "primevue/message";
import { ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";
import Tipper from "@/Components/Tipper.vue";
import MobType from "@/Composables/GeneratedEnumObjects/Mobs-MobType.json";

const props = defineProps({
    mob: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const breadcrumbs = ref([
    { label: "Mobs", url: route("mob.index") },
    { label: props.mob.name },
]);
</script>

<template>
    <Head :title="`${mob.name}`" />
    <SimpleBreadcrumb :data="breadcrumbs" />

    <div class="px-6 lg:px-0 py-12">
        <div class="flex items-center justify-center">
            <Card>
                <template #content>
                    <div class="text-xl mb-4">{{ mob.name }}</div>
                    <div class="grid grid-cols-2 grid-rows-1 gap-2 mt-2">
                        <div class="text-left q capitalize">
                            Lv. {{ mob.level }}
                        </div>
                        <div class="text-right q1 capitalize">
                            {{ mob.type }}
                        </div>
                    </div>
                    <div
                        v-if="mob.type === MobType.Boss.value"
                        class="text-right"
                    >
                        <div class="grid grid-cols-2 grid-rows-1 gap-2 mt-2">
                            <div class="text-left capitalize">
                                <Message
                                    severity="warn"
                                    class="mb-4 inline-block"
                                >
                                    {{ mob.tier }}
                                </Message>
                            </div>
                            <div class="text-right q1 capitalize">
                                <Message
                                    severity="error"
                                    class="mb-4 inline-block"
                                >
                                    <template #icon>
                                        <img
                                            src="https://cdn-icons-png.flaticon.com/128/2545/2545603.png"
                                            class="w-4"
                                        />
                                    </template>
                                    Boss
                                </Message>
                            </div>
                        </div>
                    </div>
                    <Fieldset v-if="mob.items?.length" legend="Drops">
                        <template
                            v-for="item in mob.items"
                            :key="`items-${item.id}`"
                        >
                            <div class="mt-2">
                                <Link
                                    :href="
                                        route('item.show', { item: item.slug })
                                    "
                                >
                                    <Tipper :data="item"></Tipper>
                                </Link>
                            </div>
                        </template>
                    </Fieldset>
                    <!-- TODO: Filter out the db drops from raw drops and combine -->
                    <Fieldset v-if="mob.drops?.length" legend="Raw Drops">
                        <template
                            v-for="(item, idx) in mob.drops || []"
                            :key="`rawdrop-${idx}`"
                        >
                            <div class="mt-2 capitalize">
                                {{ item }}
                            </div>
                        </template>
                    </Fieldset>
                    <Fieldset
                        v-if="mob.deprecated_data || false"
                        legend="Possible Stats"
                    >
                        <template
                            v-for="(item, key) in mob.deprecated_data || []"
                            :key="`stats-${key}`"
                        >
                            <div class="mt-2 capitalize">
                                {{ key }}: {{ item.toLocaleString() }}
                            </div>
                        </template>
                    </Fieldset>
                </template>
            </Card>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
