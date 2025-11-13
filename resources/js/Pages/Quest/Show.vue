<script setup>
import { ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";

const props = defineProps({
    quest: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const breadcrumbs = ref([
    { label: "Quests", url: route("quests.index") },
    { label: props.quest.name },
]);
</script>

<template>
    <Head :title="`${quest.name}`" />
    <SimpleBreadcrumb :data="breadcrumbs" />

    <h1>{{ props.quest.name }}</h1>
</template>
