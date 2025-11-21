<script setup>
import { router } from "@inertiajs/vue3";
import AutoComplete from "@volt/AutoComplete.vue";
import VoltButton from "@volt/SecondaryButton.vue";
import { useToast } from "primevue/usetoast";
import { ref } from "vue";

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

const title = _.map(props.items, "name").join(" - ");

const searchModal = ref({
    visible: false,
    position: "top",
    selected: props.items || null,
    filtered: [],
    data: {},
});
const autocompleteInput = ref(null);

const goToCompare = () => {
    if (_.size(searchModal.value.selected) > 3) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Too many items selected to compare. Max of 3.",
            life: 3000,
        });
        return;
    }

    router.visit(
        route("item.multi", [
            _.map(searchModal.value.selected, "slug").join("..."),
        ]),
    );
};

const search = async (event) => {
    await axios
        .post(route("search.simple"), { search: event.query })
        .then(function (response) {
            if (_.size(response.data.data?.items) > 0) {
                searchModal.value.filtered = response.data.typesense;
                searchModal.value.data = response.data.data;
            } else {
                searchModal.value.filtered = [
                    {
                        name: "No Results Found",
                    },
                ];
            }
        });
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta property="og:type" content="website" />
    </Head>
    <SimpleBreadcrumb :data="breadcrumbs" />

    <div class="max-w-2xl mx-auto">
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
            >
                <template #option="slotProps">
                    <div v-if="slotProps.option.slug" class="ml-2 w-full">
                        <div>
                            <div
                                v-if="
                                    slotProps.option.type ===
                                    SearchableType.Item.value
                                "
                                class="flex"
                            >
                                <div class="mr-2">
                                    <i
                                        class="pi pi-book"
                                        :title="slotProps.option.type"
                                    />
                                </div>
                                <div>
                                    <Tipper
                                        :title-only="true"
                                        :data="
                                            searchModal.data.items[
                                                slotProps.option.model_id
                                            ]
                                        "
                                    ></Tipper>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="h-full">No Result</div>
                </template>
            </AutoComplete>
            <VoltButton
                label="Compare"
                icon="pi pi-plus"
                pt:root="rounded-s-none"
                @click="goToCompare"
            />
        </div>

        <div class="flex justify-center">
            <template v-if="items.length">
                <div v-for="item in items" :key="item.id" class="mr-4">
                    <Tipper :data="item" :force-display="true"></Tipper>
                </div>
            </template>
            <div v-else>
                <span class="text-xl">Search for up to 3 items to compare</span>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
