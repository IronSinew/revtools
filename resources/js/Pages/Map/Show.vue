<script setup>
import { Link, router } from "@inertiajs/vue3";
import Drawer from "@volt/Drawer.vue";
import Accordion from "primevue/accordion";
import AccordionContent from "primevue/accordioncontent";
import AccordionHeader from "primevue/accordionheader";
import AccordionPanel from "primevue/accordionpanel";
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import Slider from "primevue/slider";
import { h, onMounted, ref, watch } from "vue";

import Tipper from "@/Components/Tipper.vue";
import MobType from "@/Composables/GeneratedEnumObjects/Mobs-MobType.json";
import RoomExitType from "@/Composables/GeneratedEnumObjects/Regions-RoomExitType.json";

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

const selectedRoom = ref(null);

const zoom = ref({
    current: 1,
    max: 5,
    min: 0.1,
});

const highlights = ref({
    mobs: false,
    items: false,
    bosses: false,
});

onMounted(() => {
    offset.value = { x: map.value.width / 2, y: map.value.height / 2 };
    drawRooms();
});

const getRoomAtPosition = (canvasX, canvasY) => {
    const scaledSize = roomSize * zoom.value.current;

    return props.region.rooms
        .filter((room) => room.coordinates.z === currentZ.value)
        .find((room) => {
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

const drawRoom = (room, ctx, size, color) => {
    ctx.fillStyle = color ?? room.terrain_color;
    const roomPosition = {
        x: room.coordinates.x * size + offset.value.x,
        y: -room.coordinates.y * size + offset.value.y,
    };

    ctx.fillRect(roomPosition.x, roomPosition.y, size, size);

    drawRoomBorder(room, roomPosition, ctx, size);
};

const drawRoomBorder = (room, roomPosition, ctx, size) => {
    const bosses = room.mobs.filter((mob) => mob.type === MobType.Boss.value);

    if (room.items.length > 0 && highlights.value.items) {
        ctx.strokeStyle = "#29aecc";
        ctx.lineWidth = 3;
    } else if (bosses.length > 0 && highlights.value.bosses) {
        ctx.strokeStyle = "#d432e3";
        ctx.lineWidth = 3;
    } else if (room.mobs.length > 0 && highlights.value.mobs) {
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

    roomsAtCurrentZ
        .filter((room) => room.items.length > 0)
        .forEach((room) => {
            drawRoom(room, ctx, scaledSize);
        });

    if (selectedRoom.value) {
        const selectedRoomPosition = {
            x: selectedRoom.value.coordinates.x * scaledSize + offset.value.x,
            y: -selectedRoom.value.coordinates.y * scaledSize + offset.value.y,
        };

        ctx.strokeStyle = "blue";
        ctx.lineWidth = 4;
        ctx.strokeRect(
            selectedRoomPosition.x,
            selectedRoomPosition.y,
            scaledSize,
            scaledSize,
        );
    }

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
const handleMobClick = (mob) => {
    router.get(
        route("mob.show", {
            mob: mob.slug,
        }),
    );
};

const handleRegionExitClick = () => {
    router.get(
        route("region.show", {
            region: selectedRoom.value.exit_region.slug,
        }),
    );
};

const handleZLevelChange = (value) => {
    const rooms = props.region.rooms.filter(
        (room) => room.coordinates.z === currentZ.value + value,
    );

    if (rooms.length > 0) {
        currentZ.value += value;
        selectedRoom.value = null;
        drawRooms();
    }
};

const handleCanvasClick = (event) => {
    const rect = map.value.getBoundingClientRect();
    const room = getRoomAtPosition(
        event.clientX - rect.left,
        event.clientY - rect.top,
    );

    if (room) {
        selectedRoom.value = room;
    }
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
                    <div class="grid grid-cols-2 gap-2 mt-2 ml-2 w-full">
                        <div class="items-center flex">
                            <Checkbox
                                v-model="highlights.mobs"
                                binary
                                class="cursor-pointer"
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
                                v-model="highlights.bosses"
                                binary
                                class="cursor-pointer"
                                input-id="bosses-checkbox"
                                @change="drawRooms"
                            >
                            </Checkbox>
                            <label
                                class="ml-4 cursor-pointer"
                                for="bosses-checkbox"
                            >
                                Bosses
                            </label>
                        </div>

                        <div class="items-center flex">
                            <Checkbox
                                v-model="highlights.items"
                                binary
                                class="cursor-pointer"
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
                    Z-Level: {{ currentZ }}
                    <Button
                        icon="pi pi-arrow-up"
                        size="small"
                        variant="outlined"
                        rounded
                        severity="contrast"
                        @click="handleZLevelChange(1)"
                    ></Button>
                    <Button
                        icon="pi pi-arrow-down"
                        size="small"
                        variant="outlined"
                        rounded
                        severity="contrast"
                        @click="handleZLevelChange(-1)"
                    ></Button>
                </label>
            </div>
        </div>
    </Drawer>
    <Drawer
        v-model:visible="selectedRoom"
        header="Selected Room"
        :dismissable="false"
        :modal="false"
        position="right"
        class="w-full! md:w-80! lg:w-[30rem]!"
        size="lg"
    >
        <div class="flex flex-col gap-8">
            <div>
                <h1 class="text-xl font-bold">
                    {{ selectedRoom.name }}
                </h1>
                <p class="pt-5 max-h-100 overflow-y-auto text-sm">
                    {{ selectedRoom.description }}
                </p>
            </div>
            <Accordion value="0">
                <AccordionPanel value="0">
                    <AccordionHeader>
                        <h1 class="text-xl font-bold">
                            Mobs in {{ selectedRoom.name }}
                        </h1>
                    </AccordionHeader>
                    <AccordionContent>
                        <Button
                            v-for="mob in selectedRoom.mobs"
                            :key="mob.id"
                            class="w-full flex justify-between"
                            severity="secondary mt-2"
                            @click="handleMobClick(mob)"
                        >
                            <p class="w-full text-left">{{ mob.name }}</p>
                            <p class="w-full text-right q">
                                Lvl: {{ mob.level }}
                            </p>
                        </Button>
                    </AccordionContent>
                </AccordionPanel>
            </Accordion>
            <Accordion value="1">
                <AccordionPanel value="1">
                    <AccordionHeader>
                        <h1 class="text-xl font-bold">
                            Items in {{ selectedRoom.name }}
                        </h1>
                    </AccordionHeader>
                    <AccordionContent>
                        <div
                            v-for="item in selectedRoom.items"
                            :key="item.id"
                            class="w-full flex text-xl"
                        >
                            <Link
                                class="pb-3"
                                :href="
                                    route('item.show', {
                                        item: item.slug,
                                    })
                                "
                            >
                                <Tipper :data="item"></Tipper>
                            </Link>
                        </div>
                    </AccordionContent>
                </AccordionPanel>
            </Accordion>
            <div v-if="selectedRoom && selectedRoom.exit_region_id">
                <div class="flex items-center">
                    <h1 class="text-xl">
                        This room exits to {{ selectedRoom.exit_region.name }}
                    </h1>
                    <Button
                        icon="pi pi-arrow-right"
                        class="ml-3"
                        rounded
                        size="small"
                        severity="secondary"
                        variant="text"
                        @click="handleRegionExitClick"
                    ></Button>
                </div>
            </div>
            <div v-if="selectedRoom" class="flex">
                <h1 class="text-xl mr-5">Room Exits:</h1>
                <div
                    v-for="exit in selectedRoom.exits"
                    :key="exit"
                    class="mr-1"
                >
                    <div
                        v-if="exit === RoomExitType.North.value"
                        class="pi pi-arrow-up"
                    ></div>
                    <div
                        v-if="exit === RoomExitType.West.value"
                        class="pi pi-arrow-left"
                    ></div>
                    <div
                        v-if="exit === RoomExitType.East.value"
                        class="pi pi-arrow-right"
                    ></div>
                    <div
                        v-if="exit === RoomExitType.South.value"
                        class="pi pi-arrow-down"
                    ></div>
                    <div
                        v-if="exit === RoomExitType.Down.value"
                        class="pi pi-angle-double-down"
                    ></div>
                    <div
                        v-if="exit === RoomExitType.Up.value"
                        class="pi pi-angle-double-up"
                    ></div>
                </div>
            </div>
        </div>
    </Drawer>
    <Button
        v-if="!filtersVisible"
        class="mt-5 mb-5 pi pi-arrow-left"
        @click="filtersVisible = true"
    >
        Map Options</Button
    >
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
            @click="handleCanvasClick"
        >
        </canvas>
    </div>
</template>
