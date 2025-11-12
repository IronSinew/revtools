<script setup>
import Breadcrumb from "primevue/breadcrumb";

defineProps({
    data: Object,
});
</script>

<template>
    <Teleport to="#header-slot > .header-wrapper" defer>
        <Breadcrumb :model="data">
            <template #item="{ item }">
                <Link
                    class="cursor-pointer p-breadcrumb-item-link"
                    :href="item.url"
                >
                    <span
                        v-if="item.icon"
                        :class="item.icon"
                        class="p-breadcrumb-item-icon pi pi-warehouse mr-1"
                    ></span>
                    <span
                        v-if="item.label"
                        class="p-breadcrumb-item-label"
                        v-text="item.label"
                    />
                </Link>
            </template>
        </Breadcrumb>
    </Teleport>
</template>
