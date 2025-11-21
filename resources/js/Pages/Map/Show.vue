<script setup>
import { Link, router } from "@inertiajs/vue3";
import Drawer from "@volt/Drawer.vue";
import Accordion from "primevue/accordion";
import AccordionContent from "primevue/accordioncontent";
import AccordionHeader from "primevue/accordionheader";
import AccordionPanel from "primevue/accordionpanel";
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import { h, nextTick, onMounted, onUnmounted, ref, watch } from "vue";

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
    search: {
        type: [Array, Object],
        required: false,
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
const filteredRooms = ref([]);
const roomsAtZ = ref([]);

const filters = ref({
    global: "",
    highlights: {
        mobs: false,
        items: false,
        bosses: false,
    },
});

const zoom = ref({
    current: 1,
    max: 5,
    min: 0.1,
});

const canvasSize = ref({
    width: 0,
    height: 0,
});

const canvasContainer = ref(null);
let animationFrameId = null;

onMounted(() => {
    if (props.search) {
        filters.value.global = props.search;
        filterUpdate();
        currentZ.value = filteredRooms.value[0]?.coordinates.z;

        selectedRoom.value = filteredRooms.value[0];
    }

    roomsAtZ.value = props.region.rooms.filter(
        (room) => room.coordinates.z === currentZ.value,
    );

    updateCanvasSize();

    window.addEventListener("resize", updateCanvasSize);

    function animate() {
        animationFrameId = requestAnimationFrame(animate);
        drawRooms();
    }
    animate();
});

onUnmounted(() => {
    window.removeEventListener("resize", updateCanvasSize);
    if (animationFrameId !== null) {
        cancelAnimationFrame(animationFrameId);
    }
});

const updateCanvasSize = () => {
    if (canvasContainer.value) {
        canvasSize.value = {
            width: canvasContainer.value.clientWidth,
            height: canvasContainer.value.clientHeight,
        };
    }

    nextTick(() => {
        offset.value = { x: map.value.width / 2, y: map.value.height / 2 };
        drawRooms();
    });
};

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

    drawRoomBorder(ctx, roomPosition, size, 2, "#404040");
};

const drawRoomBorder = (ctx, position, size, lineWidth, color) => {
    ctx.lineWidth = lineWidth;
    ctx.strokeStyle = color;
    ctx.strokeRect(position.x, position.y, size, size);
};

const drawRooms = () => {
    const canvas = map.value;
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext("2d");
    const scaledSize = roomSize * zoom.value.current;
    drawBackground();

    roomsAtZ.value.forEach((room) => {
        drawRoom(room, ctx, scaledSize);
    });

    // if any highlights are selected, we need to redraw room borders on top
    if (
        filters.value.highlights.items ||
        filters.value.highlights.mobs ||
        filters.value.highlights.bosses
    ) {
        roomsAtZ.value
            .filter(
                (room) =>
                    (room.mobs.length > 0 || room.items.length > 0) &&
                    room !== selectedRoom.value,
            )
            .forEach((room) => {
                const bosses = room.mobs.filter(
                    (mob) => mob.type === MobType.Boss.value,
                );

                let lineWidth = 2;
                let style = "#404040";
                if (room.items.length > 0 && filters.value.highlights.items) {
                    style = "#29aecc";
                    lineWidth = 2;
                } else if (
                    bosses.length > 0 &&
                    filters.value.highlights.bosses
                ) {
                    style = "#d432e3";
                    lineWidth = 2;
                } else if (
                    room.mobs.length > 0 &&
                    filters.value.highlights.mobs
                ) {
                    style = "#FF0000";
                    lineWidth = 2;
                }

                drawRoomBorder(
                    ctx,
                    getLocalPosition(room.coordinates),
                    scaledSize,
                    lineWidth,
                    style,
                );
            });
    }

    // draw the filtered room over anything else
    if (filteredRooms.value.length > 0) {
        filteredRooms.value.forEach((filteredRoom) => {
            if (filteredRoom.name !== selectedRoom.value?.name) {
                drawRoomBorder(
                    ctx,
                    getLocalPosition(filteredRoom.coordinates),
                    scaledSize,
                    4,
                    "#FF0000",
                );
            }
        });
    }

    if (selectedRoom.value) {
        drawSelectedRoomBorder(ctx, scaledSize, Date.now(), "#00d1c3");
    }

    if (hoveredRoom.value) {
        drawRoomBorder(
            ctx,
            getLocalPosition(hoveredRoom.value.coordinates),
            scaledSize,
            4,
            "blue",
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
        roomsAtZ.value = rooms;
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

const getLocalPosition = (coordinates) => {
    return {
        x: coordinates.x * roomSize * zoom.value.current + offset.value.x,
        y: -coordinates.y * roomSize * zoom.value.current + offset.value.y,
    };
};

const filterUpdate = () => {
    filteredRooms.value = props.region.rooms.filter((room) => {
        return (
            room.items.some((item) =>
                item.name
                    .toLowerCase()
                    .includes(filters.value.global.toLowerCase()),
            ) ||
            room.mobs.some((mob) =>
                mob.name
                    .toLowerCase()
                    .includes(filters.value.global.toLowerCase()),
            )
        );
    });
    drawRooms();
};

function drawSelectedRoomBorder(ctx, size, time, color) {
    const bracketLength = size * 0.3;
    const offset = Math.sin(time * 0.004) * 2;

    const position = getLocalPosition(selectedRoom.value.coordinates);
    const x = position.x;
    const y = position.y;

    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    ctx.lineCap = "round";

    ctx.beginPath();
    ctx.moveTo(x - offset, y + bracketLength - offset);
    ctx.lineTo(x - offset, y - offset);
    ctx.lineTo(x + bracketLength - offset, y - offset);
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(x + size - bracketLength + offset, y - offset);
    ctx.lineTo(x + size + offset, y - offset);
    ctx.lineTo(x + size + offset, y + bracketLength - offset);
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(x - offset, y + size - bracketLength + offset);
    ctx.lineTo(x - offset, y + size + offset);
    ctx.lineTo(x + bracketLength - offset, y + size + offset);
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(x + size - bracketLength + offset, y + size + offset);
    ctx.lineTo(x + size + offset, y + size + offset);
    ctx.lineTo(x + size + offset, y + size - bracketLength + offset);
    ctx.stroke();
}
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
        <div class="grid grid-cols-1 gap-8">
            <div>
                <label>
                    Highlighting
                    <div class="grid grid-cols-2 gap-2 mt-2 ml-2 w-full">
                        <div class="items-center flex">
                            <Checkbox
                                v-model="filters.highlights.mobs"
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
                                v-model="filters.highlights.bosses"
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
                                v-model="filters.highlights.items"
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
                <label>Search for mobs or items</label>
                <InputText
                    v-model="filters.global"
                    class="w-full mt-2"
                    placeholder="Search..."
                    @update:model-value="filterUpdate"
                >
                </InputText>
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
        class="w-full! xl:w-60! 2xl:w-[25rem]!"
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
                            class="flex flex-col"
                        >
                            <Link
                                class="mt-4"
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
    <div class="w-full flex justify-center">
        <div
            ref="canvasContainer"
            class="h-[calc(100vh-200px)] min-w-7xl w-2/3 border overflow-auto"
            @resize="updateCanvasSize"
        >
            <canvas
                ref="map"
                class="cursor-grab"
                :class="{
                    'cursor-pointer': hoveredRoom !== undefined,
                    'cursor-grabbing': dragging,
                }"
                :width="canvasSize.width"
                :height="canvasSize.height"
                @mousedown="handleMouseDown"
                @mousemove="handleMouseMove"
                @mouseup="handleMouseUp"
                @mouseleave="handleMouseMove"
                @click="handleCanvasClick"
                @wheel="handleMouseWheel"
                @resize="updateCanvasSize"
            >
            </canvas>
        </div>
    </div>
</template>
