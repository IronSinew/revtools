<script setup>
import Select from "@volt/Select.vue";
import { ref } from "vue";

import Tipper from "@/Components/Tipper.vue";

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    options: {
        type: [Array, Object],
        required: true,
    },
});
</script>

<template>
    <label>
        {{ props.name }}
        <Select
            class="w-full text-center"
            :options="props.options"
            option-label="name"
            option-value="id"
            filter
            placeholder="(none)"
        >
            <template #option="slotProps">
                <Tipper :data="slotProps.option" title-only></Tipper>
            </template>
        </Select>
    </label>
</template>
