<script setup>
import { Link, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Card from "primevue/card";
import Divider from "primevue/divider";
import { ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";
import Tipper from "@/Components/Tipper.vue";
import QuestRewardType from "@/Composables/GeneratedEnumObjects/Quests-QuestRewardType.json";

const props = defineProps({
    quest: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
    quest_chain: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const handleQuestChainClick = (quest) => {
    router.get(
        route("quest.show", {
            quest: quest.slug,
        }),
    );
};

const breadcrumbs = ref([
    { label: "Quests", url: route("quest.index") },
    { label: props.quest.name },
]);
</script>

<template>
    <Head :title="`${quest.name}`" />
    <SimpleBreadcrumb :data="breadcrumbs" />

    <div class="px-6 lg:px-0 py-12">
        <div class="grid grid-cols-8 grid-rows-1 gap-4">
            <div class="col-span-3">
                <Card class="w-full border-1">
                    <template #content>
                        <div class="text-xl mb-4 text-center">
                            {{ quest.name }}
                        </div>
                        <div>
                            <div class="text-xl text-center q capitalize pb-4">
                                Lv. {{ quest.level }}
                            </div>
                            <div class="text-xl text-center q capitalize pb-4">
                                Gold: {{ quest.gold.toLocaleString() }}
                            </div>
                            <div class="text-xl text-center capitalize">
                                Quest Giver:
                                <Link
                                    :href="
                                        route('mob.show', {
                                            mob: quest.mob.slug,
                                        })
                                    "
                                >
                                    {{ quest.mob.name }}
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>
                <Card class="w-full border-1 mt-4">
                    <template #header>
                        <div class="text-center text-3xl text-red-500">
                            Requirements
                        </div>
                    </template>
                    <template #content>
                        <div class="text-xl capitalize text-center pb-4">
                            Class:
                            <span
                                v-for="req_class in quest.required_class"
                                :key="req_class"
                            >
                                {{ req_class + " " }}
                            </span>
                        </div>
                    </template>
                </Card>
                <Card class="w-full border-1 mt-4">
                    <template #header>
                        <div class="text-center text-3xl text-amber-600">
                            Steps
                        </div>
                    </template>
                    <template #content>
                        <div
                            v-for="(item, index) in quest.steps"
                            :key="index"
                            class="text-xl capitalize pb-2"
                        >
                            {{ index + 1 }}. {{ item }}
                        </div>
                    </template>
                </Card>
            </div>
            <div class="col-span-3">
                <Card class="w-full border-1">
                    <template #header>
                        <div class="text-center text-3xl text-pink-700">
                            Rewards
                        </div>
                    </template>
                    <template #content>
                        <div
                            class="grid grid-cols-2 text-center justify-center"
                        >
                            <div
                                v-for="item in quest.items"
                                :key="item.id"
                                class="capitalize pb-2"
                            >
                                <Link
                                    :href="
                                        route('item.show', {
                                            item: item.slug,
                                        })
                                    "
                                >
                                    <Tipper :data="item"></Tipper>
                                </Link>
                            </div>
                            <div
                                v-for="reward in quest.raw_rewards"
                                :key="reward"
                                class="capitalize pb-2"
                            >
                                <span>
                                    {{ reward.name.replaceAll("_", " ") }}
                                </span>
                                <span
                                    v-if="
                                        reward.amount > 1 &&
                                        reward.type !==
                                            QuestRewardType.Title.value
                                    "
                                    class="ml-1"
                                >
                                    {{ reward.amount }}
                                </span>
                            </div>
                        </div>
                    </template>
                </Card>
                <Card class="w-full border-1 mt-4">
                    <template #header>
                        <div class="text-center text-3xl text-blue-500">
                            Objectives
                        </div>
                    </template>
                    <template #content>
                        <div
                            v-for="(item, index) in quest.objectives"
                            :key="index"
                            class="text-xl capitalize pb-2"
                        >
                            {{ index + 1 }}. {{ item }}
                        </div>
                    </template>
                </Card>
            </div>
            <div class="col-span-2 col-start-7">
                <Card v-if="quest_chain.length" class="w-full border-1">
                    <template #header>
                        <div class="text-center text-3xl text-orange-500">
                            Quest Chain
                        </div>
                    </template>
                    <template #content>
                        <div class="text-xl capitalize pb-4">
                            <Button
                                v-for="(chain, index) in quest_chain"
                                :key="chain.id"
                                class="w-full mb-2"
                                :disabled="props.quest.id === chain.id"
                                :severity="[
                                    props.quest.id === chain.id
                                        ? 'info'
                                        : 'secondary',
                                ]"
                                @click="handleQuestChainClick(chain)"
                            >
                                <div
                                    class="grid grid-cols-2 w-full justify-between text-left"
                                >
                                    <div>{{ index + 1 }}. {{ chain.name }}</div>
                                    <div class="q text-right">
                                        Lv. {{ chain.level }}
                                    </div>
                                    <div>{{ chain.mob.name }}</div>
                                </div>
                            </Button>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>
