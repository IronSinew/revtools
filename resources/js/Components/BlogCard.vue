<script setup>
import { Link } from "@inertiajs/vue3";
import Skeleton from "primevue/skeleton";

defineProps({
    blog: {
        type: [Array, Object],
        default: () => {},
    },
    skeleton: {
        type: Boolean,
        default: () => false,
    },
});
</script>

<template>
    <div v-if="skeleton" class="recipe-card skeleton relative">
        <Skeleton height="200px" width="100%" />
        <div class="m-4">
            <p class="mb-2">
                <Skeleton width="100%" height="2rem" />
            </p>
            <p>
                <Skeleton width="100%" height="1rem" />
            </p>
        </div>
    </div>
    <div
        v-else
        class="blog-card transition ease-in-out hover:scale-110 relative"
    >
        <img
            :src="
                blog.hero_preview
                    ? blog.hero_preview
                    : '/assets/no-recipe-img.png'
            "
            :alt="blog.name"
            class="w-full h-[32] sm:h-[322px] object-cover"
        />
        <div class="my-4">
            <h2 class="font-bold text-xl">{{ blog.title }}</h2>
            <p>{{ blog.excerpt }}</p>
        </div>
        <Link
            v-if="!skeleton"
            :href="route('recipe.show', { recipe: blog.slug })"
            class="absolute inset-0"
        />
    </div>
</template>
