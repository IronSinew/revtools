<script setup>
import Card from "@volt/Card.vue";
import Select from "@volt/Select.vue";

import GearSlot from "@/Components/GearSlot.vue";

const props = defineProps({
    helms: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
    necks: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});
</script>

<template>
    <h1 class="text-center text-2xl m-5">Gear Planner</h1>

    <div class="flex justify-center">
        <Card class="w-1/2">
            <template #content>
                <div class="grid grid-cols-3 grid-rows-7 gap-4 text-center">
                    <div class="col-start-2">
                        <GearSlot :options="props.helms" name="Helm"></GearSlot>
                    </div>
                    <div class="col-start-3 row-start-2">
                        <label>
                            Neck
                            <GearSlot
                                :options="props.necks"
                                name="Neck"
                            ></GearSlot>
                        </label>
                    </div>
                    <div class="col-start-3 row-start-3">
                        <label>
                            Wrist
                            <Select class="w-full" />
                        </label>
                    </div>
                    <div class="col-start-2 row-start-3">Chest</div>
                    <div class="col-start-1 row-start-3">Wrist</div>
                    <div class="row-start-4">Main Hand</div>
                    <div class="col-start-1 row-start-5">Finger</div>
                    <div class="col-start-3 row-start-4">Off Hand</div>
                    <div class="col-start-3 row-start-5">Finger</div>
                    <div class="col-start-2 row-start-5">Hands</div>
                    <div class="col-start-2 row-start-6">Legs</div>
                    <div class="col-start-2 row-start-7">Feet</div>
                </div>
            </template>
        </Card>
    </div>
</template>
