<script setup>
import { Link, router, useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import Card from "primevue/card";
import Fieldset from "primevue/fieldset";
import { useToast } from "primevue/usetoast";
import { onMounted, ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";
import Tipper from "@/Components/Tipper.vue";

const props = defineProps({
    item: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const toast = useToast();
const breadcrumbs = ref([
    { label: "Items", url: route("item.index") },
    { label: props.item.name },
]);
</script>

<template>
    <Head :title="`${item.name}`" />
    <SimpleBreadcrumb :data="breadcrumbs" />

    <div class="px-6 lg:px-0 py-12">
        <div class="flex items-center justify-center">
            <Tipper :data="item" :force-display="true"></Tipper>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
