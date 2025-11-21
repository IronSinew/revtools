<script setup>
import { router, usePage } from "@inertiajs/vue3";
import Drawer from "@volt/Drawer.vue";
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import Slider from "primevue/slider";
import {nextTick, onMounted, onUnmounted, ref, watch} from "vue";

import ClassType from "@/Composables/GeneratedEnumObjects/ClassType.json";
import QuestRewardType from "@/Composables/GeneratedEnumObjects/Quests-QuestRewardType.json";

const props = defineProps({
    regions: {
        type: [Array, Object],
        required: false,
        default() {
            return {};
        },
    },
});

onMounted(() => {
    updateCanvasSize();

    window.addEventListener("resize", updateCanvasSize);
});

onUnmounted(() => window.removeEventListener("resize", updateCanvasSize));

const updateCanvasSize = () => {
    if (canvasContainer.value) {
        canvasSize.value = {
            width: canvasContainer.value.clientWidth,
            height: canvasContainer.value.clientHeight,
        };
    }

    nextTick(() => {
        offset.value = { x: map.value.width / 2, y: map.value.height / 2 };
        drawRegionMap();
    });
};

const map = ref(null);
const gridSize = 200;
const regionRadius = 10;

const filtersVisible = ref(true);

const dragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });
const offset = ref({ x: 400, y: 300 });
const hoveredRegion = ref(null);
const showConnections = ref(false);

const canvasSize = ref({
    width: 0,
    height: 0,
});
const canvasContainer = ref(null);

const zoom = ref({
    current: 1,
    max: 1.7,
    min: 0.7,
});

const getRegionAtPosition = (canvasX, canvasY) => {
    return props.regions.find((region) => {
        const x =
            region.coordinates.x * gridSize * zoom.value.current +
            offset.value.x;
        const y =
            region.coordinates.y * gridSize * zoom.value.current +
            offset.value.y;

        return (
            Math.sqrt(Math.pow(canvasX - x, 2) + Math.pow(canvasY - y, 2)) <=
            regionRadius * zoom.value.current
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
        const region = getRegionAtPosition(
            event.clientX - rect.left,
            event.clientY - rect.top,
        );

        if (region !== hoveredRegion.value) {
            hoveredRegion.value = region;
        }
    } else {
        offset.value = {
            x: event.clientX - dragStart.value.x,
            y: event.clientY - dragStart.value.y,
        };
    }

    drawRegionMap();
};

const handleMouseUp = () => {
    dragging.value = false;
};

const getLocalPosition = (coordinates) => {
    return {
        x: coordinates.x * gridSize * zoom.value.current + offset.value.x,
        y: coordinates.y * gridSize * zoom.value.current + offset.value.y,
    };
};

watch(
    () => props.regions,
    () => {
        drawRegionMap();
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
        x: canvas.width * 3,
        y: canvas.height * 3,
    };

    ctx.fillStyle = "#333333";
    ctx.fillRect(-mapSize.x / 2, -mapSize.y / 2, mapSize.x, mapSize.y);
};

const drawRegionMap = () => {
    const canvas = map.value;
    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext("2d");
    drawBackground();
    const labelOffset = 10 * zoom.value.current;
    const scaledSize = gridSize * zoom.value.current;
    const fontSize = 18 * zoom.value.current;

    // draw connections
    if (showConnections.value) {
        props.regions.forEach((region) => {
            const regionPosition = getLocalPosition(region.coordinates);

            region.connections
                .map((regionFromId) =>
                    props.regions.find((r) => r.id === regionFromId),
                )
                .filter((x) => x !== undefined)
                .forEach((connectedRegion) => {
                    const connectedRegionPosition = getLocalPosition({
                        x: connectedRegion.coordinates.x,
                        y: connectedRegion.coordinates.y,
                    });
                    ctx.lineWidth = 2;
                    ctx.setLineDash([5, 10]);
                    ctx.beginPath();
                    ctx.moveTo(regionPosition.x, regionPosition.y);
                    ctx.lineTo(
                        connectedRegionPosition.x,
                        connectedRegionPosition.y,
                    );
                    ctx.stroke();
                });
        });
    }

    props.regions.forEach((region) => {
        const regionPosition = getLocalPosition(region.coordinates);

        const hovered = hoveredRegion.value?.id === region.id;

        ctx.beginPath();
        ctx.arc(
            regionPosition.x,
            regionPosition.y,
            regionRadius * zoom.value.current,
            0,
            Math.PI * 2,
        );
        ctx.fillStyle = hovered ? "#22c55e" : region.color;
        ctx.fill();
        ctx.strokeStyle = "#000000";
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.font = `${fontSize}px serif`;
        const textMetrics = ctx.measureText(region.name);

        ctx.fillStyle = "#FFFFFF";
        ctx.fillText(
            region.name,
            regionPosition.x - textMetrics.width / 2,
            regionPosition.y - regionRadius - labelOffset,
            scaledSize,
        );
    });
};

const handleCanvasClick = (event) => {
    const rect = map.value.getBoundingClientRect();
    const region = getRegionAtPosition(
        event.clientX - rect.left,
        event.clientY - rect.top,
    );

    if (region) {
        router.get(route("region.show", { region: region.slug }));
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

    drawRegionMap();
};
</script>

<template>
    <Head :title="`Map`" />
    <Drawer
        v-model:visible="filtersVisible"
        header="Filters"
        :dismissable="false"
        :modal="false"
    >
        <div class="grid grid-cols-1 gap-8">
            <div class="flex items-center">
                <Checkbox
                    v-model="showConnections"
                    binary
                    class="w-full cursor-pointer"
                    input-id="connections-checkbox"
                    @change="drawRegionMap()"
                >
                </Checkbox>
                <label class="ml-4 cursor-pointer" for="connections-checkbox">
                    Show Region Connections
                </label>
            </div>
        </div>
    </Drawer>
    <div
        ref="canvasContainer"
        class="h-[calc(100vh-80px)] min-w-7xl w-full border overflow-auto"
        @resize="updateCanvasSize"
    >
        <canvas
            ref="map"
            class="cursor-grab border-brown-500"
            :class="{
                'cursor-pointer': hoveredRegion !== undefined,
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
</template>
