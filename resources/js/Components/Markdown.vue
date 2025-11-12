<script setup>
import { marked } from "marked";
import { computed } from "vue";

const props = defineProps({
    body: { type: [String, null], required: false, default: "" },
});

const markdownToHtml = computed(() => marked.parse(props.body || ""));
</script>

<template>
    <div class="markdown-container">
        <div v-html="markdownToHtml" />
    </div>
</template>

<style lang="scss" scoped>
.markdown-container {
    &:deep(p) {
        @apply mb-2;
    }

    &:deep(h1),
    &:deep(h2),
    &:deep(h3),
    &:deep(h4),
    &:deep(h5) {
        @apply mb-3;
    }

    &:deep(a) {
        @apply text-primary-400;
    }
}
</style>
