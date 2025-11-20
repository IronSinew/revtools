<script setup>
import { router } from "@inertiajs/vue3";
import Button from "primevue/button";
import { onMounted, ref, watch } from "vue";

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

const zoom = ref({
    current: 1,
    max: 5,
    min: 0.1,
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

const drawRooms = () => {
    const canvas = map.value;
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext("2d");
    const scaledSize = roomSize * zoom.value.current;
    drawBackground();

    props.region.rooms
        .filter((room) => room.coordinates.z === currentZ.value)
        .forEach((room) => {
            ctx.fillStyle = room.terrain_color;
            const roomPosition = {
                x: room.coordinates.x * scaledSize + offset.value.x,
                y: -room.coordinates.y * scaledSize + offset.value.y,
            };

            ctx.fillRect(
                roomPosition.x,
                roomPosition.y,
                scaledSize,
                scaledSize,
            );

            ctx.strokeStyle = "#404040";
            ctx.lineWidth = 2;
            ctx.strokeRect(
                roomPosition.x,
                roomPosition.y,
                scaledSize,
                scaledSize,
            );
        });

    if (hoveredRoom.value) {
        const hoveredRoomPosition = {
            x: hoveredRoom.value.coordinates.x * scaledSize + offset.value.x,
            y: -hoveredRoom.value.coordinates.y * scaledSize + offset.value.y,
        };

        ctx.strokeStyle = "#700000";
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
    <Button class="mt-5 mb-5 pi pi-arrow-left"> Map Options</Button>
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
