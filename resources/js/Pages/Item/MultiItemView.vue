<script setup>
import { router } from "@inertiajs/vue3";
import AutoComplete from "@volt/AutoComplete.vue";
import VoltButton from "@volt/SecondaryButton.vue";
import { useToast } from "primevue/usetoast";
import { computed, nextTick, onMounted, ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";
import Tipper from "@/Components/Tipper.vue";
import SearchableType from "@/Composables/GeneratedEnumObjects/SearchableType.json";

const toast = useToast();

const props = defineProps({
    items: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const breadcrumbs = ref([
    { label: "Items", url: route("item.index") },
    { label: "Multi Item View" },
]);

const searchModal = ref({
    visible: false,
    position: "top",
    selected: [],
    filtered: [],
    data: {},
});

const title = computed(() =>
    _.map(searchModal.value.selected, "name").join(" - "),
);

const autocompleteInput = ref(null);
const onItemSelect = () => {
    if (_.size(searchModal.value.selected) > 3) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Too many items selected to compare. Max of 3.",
            life: 3000,
        });
        searchModal.value.selected = searchModal.value.selected.slice(0, 3);
    }

    window.history.pushState(
        JSON.parse(JSON.stringify(searchModal.value.selected)),
        title,
        route("item.multi", [
            _.map(searchModal.value.selected, "slug").join("..."),
        ]),
    );
};

const search = async (event) => {
    await axios
        .post(route("search.simple"), { search: event.query })
        .then(function (response) {
            if (Object.values(response.data.data?.items).length > 0) {
                searchModal.value.filtered = Object.values(
                    response.data.data.items,
                );
            } else {
                searchModal.value.filtered = [
                    {
                        name: "No Results Found",
                    },
                ];
            }
        });
};

onMounted(() => {
    const items = JSON.parse(JSON.stringify(props.items));

    if (_.size(items) > 0) {
        searchModal.value.selected = items;
    }
});
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta property="og:type" content="website" />
    </Head>
    <SimpleBreadcrumb :data="breadcrumbs" />

    <div class="max-w-2xl mx-auto">
        <div class="text-center my-4">
            <span class="text-xl">Search for up to 3 items to compare</span>
        </div>
        <div class="flex items-stretch w-full my-4">
            <AutoComplete
                id="mainSearch"
                ref="autocompleteInput"
                v-model="searchModal.selected"
                :suggestions="searchModal.filtered"
                option-label="name"
                multiple
                fluid
                field="name"
                placeholder="Items to compare"
                pt:root="flex-1 rounded-e-none rounded-s-md"
                scroll-height="350px"
                @complete="search($event)"
                @item-select="onItemSelect"
                @item-unselect="onItemSelect"
            >
                <template #option="slotProps">
                    <div v-if="slotProps.option.slug" class="ml-2 w-full">
                        <div class="flex">
                            <div class="mr-2">
                                <i
                                    class="pi pi-book"
                                    :title="slotProps.option.type"
                                />
                            </div>
                            <div>
                                <Tipper
                                    :title-only="true"
                                    :data="slotProps.option"
                                ></Tipper>
                            </div>
                        </div>
                    </div>
                    <div v-else class="h-full">No Result</div>
                </template>
            </AutoComplete>
        </div>

        <div class="flex justify-center">
            <template v-if="searchModal.selected.length">
                <div
                    v-for="item in searchModal.selected"
                    :key="item.id"
                    class="mr-4"
                >
                    <Tipper :data="item" :force-display="true"></Tipper>
                </div>
            </template>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
