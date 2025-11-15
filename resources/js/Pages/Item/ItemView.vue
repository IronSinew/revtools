<script setup>
import html2canvas from "html2canvas";
import { onMounted, ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";
import Tipper from "@/Components/Tipper.vue";

const ogBase64 = ref();

const props = defineProps({
    item: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const breadcrumbs = ref([
    { label: "Items", url: route("item.index") },
    { label: props.item.name },
]);

const tooltipContainer = ref(null);
onMounted(() => {
    const invisibleDiv = document.createElement("div");
    invisibleDiv.innerHTML = tooltipContainer.value.innerHTML;
    document.body.appendChild(invisibleDiv);

    html2canvas(invisibleDiv, { width: 400, backgroundColor: null }).then(
        (canvas) => {
            ogBase64.value = canvas.toDataURL("image/png");

            document.body.removeChild(invisibleDiv);
        },
    );
});
</script>

<template>
    <Head>
        <title>{{ item.name }}</title>
        <meta
            property="og:image"
            :content="route('decode.img', { q: JSON.stringify(ogBase64) })"
        />
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="item.name" />
        <meta
            property="og:description"
            :content="[item.type, item.sub_type.item.slot].filter().join(' - ')"
        />
    </Head>
    <SimpleBreadcrumb :data="breadcrumbs" />

    <div class="px-6 lg:px-0 py-12">
        <div ref="tooltipContainer" class="flex items-center justify-center">
            <Tipper :data="item" :force-display="true"></Tipper>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
