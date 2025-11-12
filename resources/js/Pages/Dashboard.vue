<script setup>
import { Link, router } from "@inertiajs/vue3";
import AutoComplete from "primevue/autocomplete";
import { ref } from "vue";

defineProps({
    bar: {
        type: [Array, Object],
        default: () => [],
    },
    foo: {
        type: String,
        default: "baz",
    },
});
</script>

<template>
    <Head title="Home" />
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            More stuff coming soon...
        </div>
    </div>
</template>

<style lang="scss" scoped>
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease;
}
</style>
