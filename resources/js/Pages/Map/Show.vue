<script setup>
import { router } from "@inertiajs/vue3";
import Drawer from "@volt/Drawer.vue";
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import Slider from "primevue/slider";
import {h, onMounted, ref, watch} from "vue";

import ClassType from "@/Composables/GeneratedEnumObjects/ClassType.json";
import QuestRewardType from "@/Composables/GeneratedEnumObjects/Quests-QuestRewardType.json";

const props = defineProps({
    region: {
        type: [Array, Object],
        required: false,
        default() {
            return {};
        },
    },
});

const map = ref(null);
const roomSize = 30;
const currentZ = ref(0);

const dragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const offset = ref({ x: 0, y: 0 });

const hoveredRoom = ref(null);
const filtersVisible = ref(true);

const zoom = ref({
    current: 1,
    max: 5,
    min: 0.1,
});

const highlights = ref({
    mobs: false,
    items: false,
});

onMounted(() => {
    offset.value = { x: map.value.width / 2, y: map.value.height / 2 };
    drawRooms();
});

const getRoomAtPosition = (canvasX, canvasY) => {
    const scaledSize = roomSize * zoom.value.current;

    return props.region.rooms.find((room) => {
        const x = room.coordinates.x * scaledSize + offset.value.x;
        const y = -room.coordinates.y * scaledSize + offset.value.y;

        return (
            canvasX >= x &&
            canvasX <= x + scaledSize &&
            canvasY >= y &&
            canvasY <= y + scaledSize
        );
    });
};

const handleMouseDown = (event) => {
    dragging.value = true;
    dragStart.value = {
        x: event.clientX - offset.value.x,
        y: event.clientY - offset.value.y,
    };
};

const handleMouseMove = (event) => {
    const rect = map.value.getBoundingClientRect();

    if (!dragging.value) {
        const room = getRoomAtPosition(
            event.clientX - rect.left,
            event.clientY - rect.top,
        );

        if (room !== hoveredRoom.value) {
            hoveredRoom.value = room;
        }
    } else {
        offset.value = {
            x: event.clientX - dragStart.value.x,
            y: event.clientY - dragStart.value.y,
        };
    }

    drawRooms();
};

const handleMouseUp = () => {
    dragging.value = false;
};

watch(
    () => props.region,
    () => {
        drawRooms();
    },
    { deep: true },
);

const drawBackground = () => {
    const canvas = map.value;
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const mapSize = {
        x: canvas.width * 2,
        y: canvas.height * 2,
    };

    ctx.fillStyle = "#333333";
    ctx.fillRect(-mapSize.x / 2, -mapSize.y / 2, mapSize.x, mapSize.y);
};

const drawRoom = (room, ctx, size) => {
    ctx.fillStyle = room.terrain_color;
    const roomPosition = {
        x: room.coordinates.x * size + offset.value.x,
        y: -room.coordinates.y * size + offset.value.y,
    };

    ctx.fillRect(roomPosition.x, roomPosition.y, size, size);

    drawRoomBorder(room, roomPosition, ctx, size);
};

const drawRoomBorder = (room, roomPosition, ctx, size) => {
    const items = room.mobs.forEach((mob) => {
        if (mob.items.length > 0) {
            return true;
        }
    });
    console.log(items);
    if (room.mobs.length > 0 && highlights.value.mobs) {
        ctx.strokeStyle = "#FF0000";
        ctx.lineWidth = 3;
    } else {
        ctx.strokeStyle = "#404040";
        ctx.lineWidth = 2;
    }

    ctx.strokeRect(roomPosition.x, roomPosition.y, size, size);
};

const drawRooms = () => {
    const canvas = map.value;
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext("2d");
    const scaledSize = roomSize * zoom.value.current;
    drawBackground();

    const roomsAtCurrentZ = props.region.rooms.filter(
        (room) => room.coordinates.z === currentZ.value,
    );

    roomsAtCurrentZ
        .filter((room) => room.mobs.length === 0)
        .forEach((room) => {
            drawRoom(room, ctx, scaledSize);
        });

    roomsAtCurrentZ
        .filter((room) => room.mobs.length > 0)
        .forEach((room) => {
            drawRoom(room, ctx, scaledSize);
        });

    if (hoveredRoom.value) {
        const hoveredRoomPosition = {
            x: hoveredRoom.value.coordinates.x * scaledSize + offset.value.x,
            y: -hoveredRoom.value.coordinates.y * scaledSize + offset.value.y,
        };

        ctx.strokeStyle = "blue";
        ctx.lineWidth = 4;
        ctx.strokeRect(
            hoveredRoomPosition.x,
            hoveredRoomPosition.y,
            scaledSize,
            scaledSize,
        );
    }
};

const handleMouseWheel = (event) => {
    event.preventDefault();
    const rect = map.value.getBoundingClientRect();
    const mousePosition = {
        x: event.clientX - rect.left,
        y: event.clientY - rect.top,
    };

    const worldPosition = {
        x: (mousePosition.x - offset.value.x) / zoom.value.current,
        y: (mousePosition.y - offset.value.y) / zoom.value.current,
    };
    const delta = event.deltaY > 0 ? 0.9 : 1.1;

    zoom.value.current = Math.min(
        Math.max(zoom.value.min, zoom.value.current * delta),
        zoom.value.max,
    );
    offset.value = {
        x: mousePosition.x - worldPosition.x * zoom.value.current,
        y: mousePosition.y - worldPosition.y * zoom.value.current,
    };

    drawRooms();
};
</script>

<template>
    <Head :title="`Map`" />
    <Drawer
        v-model:visible="filtersVisible"
        header="Filters"
        :dismissable="false"
        :modal="false"
        class="w-full"
    >
        <div class="grid grid-cols-1 grid-rows-5 gap-8">
            <div>
                <label>
                    Highlighting
                    <div class="flex flex-col gap-2 mt-2 ml-2 w-full">
                        <div class="items-center flex">
                            <Checkbox
                                v-model="highlights.mobs"
                                binary
                                class="w-full cursor-pointer"
                                input-id="mob-checkbox"
                                @change="drawRooms"
                            >
                            </Checkbox>
                            <label
                                class="ml-4 cursor-pointer"
                                for="mob-checkbox"
                            >
                                Mobs
                            </label>
                        </div>

                        <div class="items-center flex">
                            <Checkbox
                                v-model="highlights.items"
                                binary
                                class="w-full cursor-pointer"
                                input-id="item-checkbox"
                                @change="drawRooms"
                            >
                            </Checkbox>
                            <label
                                class="ml-4 cursor-pointer"
                                for="item-checkbox"
                            >
                                Items
                            </label>
                        </div>
                    </div>
                </label>
            </div>
            <div>
                <label>
                    Class Filter:
                    <Select
                        :options="jsonObjectToSelectOptions(ClassType)"
                        option-value="value"
                        option-label="label"
                        placeholder="Limit to Class"
                        class="w-full"
                        :show-clear="true"
                    />
                </label>
            </div>
            <div>
                <div class="mb-5 text-center">
                    Level range:
                    {{ 0 }}
                    -
                    {{ 100 }}
                </div>
                <Slider :min="0" :max="100" :step="5" range />
            </div>
            <div class="grid grid-cols-2 grid-rows-1 gap-2 mt-2">
                <div>
                    <Button label="Clear" severity="warn" />
                </div>
                <div class="text-right">
                    <Button label="Apply" />
                </div>
            </div>
        </div>
    </Drawer>
    <Button class="mt-5 mb-5 pi pi-arrow-left"> Map Options</Button>
    <h1 class="w-full text-center text-4xl pb-3">{{ region.name }}</h1>
    <div class="flex">
        <canvas
            ref="map"
            class="cursor-grab"
            :class="{
                'cursor-pointer': hoveredRoom !== undefined,
                'cursor-grabbing': dragging,
            }"
            width="1400"
            height="1000"
            @mousedown="handleMouseDown"
            @mousemove="handleMouseMove"
            @mouseup="handleMouseUp"
            @mouseleave="handleMouseMove"
            @wheel="handleMouseWheel"
        >
        </canvas>
    </div>
</template>
