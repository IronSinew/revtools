<script setup>
import { Deferred, Link, router, usePage } from "@inertiajs/vue3";
import Drawer from "@volt/Drawer.vue";
import Popover from "@volt/Popover.vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Fieldset from "primevue/fieldset";
import Message from "primevue/message";
import MultiSelect from "primevue/multiselect";
import Select from "primevue/select";
import Slider from "primevue/slider";
import { ref } from "vue";

import Tipper from "@/Components/Tipper.vue";
import ClassType from "@/Composables/GeneratedEnumObjects/ClassType.json";
import ItemSlot from "@/Composables/GeneratedEnumObjects/Items-ItemSlot.json";
import ItemSubTypeEnum from "@/Composables/GeneratedEnumObjects/Items-ItemSubType.json";
import ItemTypeEnum from "@/Composables/GeneratedEnumObjects/Items-ItemType.json";
import dataTablesSortFromProps from "@/Composables/sort-props-for-datatables.js";

defineProps({
    table: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const asAALevel = (inputVal) => {
    if (inputVal <= 100) {
        return inputVal;
    }

    return `AA${inputVal % 100}`;
};

const page = usePage();
const loading = ref(false);
const defaultFilters = {
    class: { value: null },
    type: { value: [] },
    sub_type: { value: [] },
    slot: { value: [] },
    effective_required_level: { value: [0, 150] },
};

const lazyParams = ref({
    page: parseInt(page.props.params?.page || 0),
    last: parseInt(page.props.params?.last || 0),
    rows: parseInt(page.props.params?.rows || 25),
    sort: [...dataTablesSortFromProps(page.props.params?.sort)],
    filters: {
        ...JSON.parse(JSON.stringify(defaultFilters)),
        ...(page.props.params?.filters || {}),
    },
});

const clearFilters = () => {
    lazyParams.value.sort = [];
    lazyParams.value.filters = JSON.parse(JSON.stringify(defaultFilters));
};

const applyDataLazyLoad = (event) => {
    lazyParams.value = {
        ...lazyParams.value,
        ...{
            page: event.page,
            rows: event.rows,
            sort: event.multiSortMeta,
        },
    };

    loadLazyData();
};

const loadLazyData = () => {
    loading.value = true;

    router.get(route("item.index"), lazyParams.value, {
        only: ["table"],
        onFinish: (visit) => {
            loading.value = false;
        },
        replace: true,
        preserveState: true,
    });
};

const filterDrawer = ref(true);

const popovers = ref([]);
const togglePopover = (event, index) => {
    const popover = popovers.value[index];
    if (popover) {
        popover.toggle(event);
    }
};
</script>

<template>
    <Head :title="`Items`" />

    <div class="px-6 lg:px-0 py-12">
        <Drawer
            v-model:visible="filterDrawer"
            header="Filters"
            :dismissable="false"
            :modal="false"
        >
            <div class="grid grid-cols-1 grid-rows-5 gap-8">
                <div>
                    <label>
                        Type:
                        <MultiSelect
                            v-model="lazyParams.filters.type.value"
                            :options="jsonObjectToSelectOptions(ItemTypeEnum)"
                            option-value="value"
                            option-label="name"
                            placeholder="Select Type"
                            class="w-full"
                            :show-clear="true"
                        />
                    </label>
                </div>
                <div>
                    <label>
                        Sub type:
                        <MultiSelect
                            v-model="lazyParams.filters.sub_type.value"
                            :options="
                                jsonObjectToSelectOptions(ItemSubTypeEnum)
                            "
                            filter
                            option-value="value"
                            option-label="label"
                            placeholder="Select Sub Type"
                            class="w-full"
                            :show-clear="true"
                        />
                    </label>
                </div>
                <div>
                    <label>
                        Item Slot:
                        <MultiSelect
                            v-model="lazyParams.filters.slot.value"
                            :options="jsonObjectToSelectOptions(ItemSlot)"
                            filter
                            option-value="value"
                            option-label="name"
                            placeholder="Select Item Slot"
                            class="w-full"
                            :show-clear="true"
                        />
                    </label>
                </div>
                <div>
                    <label>
                        Class Filter:
                        <Select
                            v-model="lazyParams.filters.class.value"
                            :options="jsonObjectToSelectOptions(ClassType)"
                            option-value="value"
                            option-label="name"
                            placeholder="Limit to Class"
                            class="w-full"
                            :show-clear="true"
                        />
                    </label>
                </div>
                <div>
                    <div class="mb-5 text-center">
                        Level range:
                        {{
                            asAALevel(
                                lazyParams.filters.effective_required_level
                                    .value[0],
                            )
                        }}
                        -
                        {{
                            asAALevel(
                                lazyParams.filters.effective_required_level
                                    .value[1],
                            )
                        }}
                    </div>
                    <Slider
                        v-model="
                            lazyParams.filters.effective_required_level.value
                        "
                        :min="0"
                        :max="175"
                        :step="5"
                        range
                    />
                </div>
                <div class="grid grid-cols-2 grid-rows-1 gap-2 mt-2">
                    <div>
                        <Button
                            label="Clear"
                            severity="warn"
                            @click="clearFilters"
                        />
                    </div>
                    <div class="text-right">
                        <Button label="Apply" @click="loadLazyData" />
                    </div>
                </div>
            </div>
        </Drawer>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 grid-rows-1 gap-2 mt-2">
                <div>
                    <Button
                        v-if="!filterDrawer"
                        label="Show Filters"
                        class="mb-4"
                        @click="filterDrawer = true"
                    />
                </div>
                <div class="text-right">
                    <Message
                        severity="info"
                        icon="pi pi-info-circle"
                        class="mb-4 inline-block"
                    >
                        Hold ctrl to sort multiple columns
                    </Message>
                </div>
            </div>
            <Deferred data="table">
                <DataTable
                    v-model:filters="lazyParams.filters"
                    v-model:multi-sort-meta="lazyParams.sort"
                    filter-display="menu"
                    data-key="id"
                    class="p-datatable list-view"
                    sort-mode="multiple"
                    :removable-sort="true"
                    :striped-rows="true"
                    :value="table.data"
                    :paginator="!!table.data"
                    :total-records="table.total"
                    :rows="lazyParams.rows"
                    :lazy="true"
                    :loading="loading"
                    :default-sort-order="-1"
                    :rows-per-page-options="[10, 25, 50]"
                    @page="applyDataLazyLoad($event)"
                    @sort="applyDataLazyLoad($event)"
                    @filter="applyDataLazyLoad($event)"
                >
                    <template #empty> No Items</template>
                    <Column field="id" header="ID" class="tight-column" />
                    <Column
                        field="type"
                        header="Type"
                        class="tight-column whitespace-nowrap"
                    >
                        <template #body="prop">
                            {{
                                jsonObjectGetLabelByValue(
                                    ItemTypeEnum,
                                    prop.data.type,
                                )
                            }}
                        </template>
                    </Column>
                    <Column
                        field="sub_type"
                        header="Sub Type"
                        class="tight-column whitespace-nowrap"
                    >
                        <template #body="prop">
                            {{
                                jsonObjectGetLabelByValue(
                                    ItemSubTypeEnum,
                                    prop.data.sub_type,
                                )
                            }}
                        </template>
                    </Column>
                    <Column field="slot" header="Slot" class="tight-column">
                        <template #body="prop">
                            <span v-if="prop.data.slot">
                                {{
                                    jsonObjectGetLabelByValue(
                                        ItemSlot,
                                        prop.data.slot,
                                    )
                                }}
                            </span>
                        </template>
                    </Column>
                    <Column field="drops" header="Dropped By">
                        <template #body="prop">
                            <div class="text-center">
                                <Button
                                    v-if="prop.data.mobs?.length"
                                    icon="pi pi-eye"
                                    @click="togglePopover($event, prop.index)"
                                />
                            </div>
                            <Popover :ref="(el) => (popovers[prop.index] = el)">
                                <Fieldset
                                    v-if="prop.data.mobs?.length"
                                    legend="Dropped By"
                                >
                                    <template
                                        v-for="mob in prop.data.mobs"
                                        :key="`item-${prop.data.id}-mobs-${mob.id}`"
                                    >
                                        <div class="mt-2">
                                            <Link :href="route('mob.show', { mob: mob.slug })">
                                                <span class="q">{{ mob.name }}</span>
                                            </Link>
                                            <br />
                                            <span class="ml-2">{{
                                                (
                                                    JSON.parse(mob.location) ||
                                                    []
                                                ).join(", ")
                                            }}</span>
                                        </div>
                                    </template>
                                </Fieldset>
                            </Popover>
                        </template>
                    </Column>
                    <Column field="name" header="Name" sortable>
                        <template #body="prop">
                            <Link
                                :href="
                                    route('item.show', { item: prop.data.slug })
                                "
                            >
                                <Tipper :data="prop.data"></Tipper>
                            </Link>
                        </template>
                    </Column>
                    <Column field="type_value" header="Armor/Damage" sortable />
                    <Column field="speed" header="Speed" sortable />
                    <Column
                        field="effective_required_level"
                        header="Req. Level"
                        sortable
                    >
                        <template #body="prop">
                            <span
                                v-if="prop.data.requirements?.aa_level"
                                class="q4"
                                >AA{{ prop.data.requirements?.aa_level }}</span
                            >
                            <span v-else>{{
                                prop.data.requirements?.level
                            }}</span>
                        </template>
                    </Column>
                    <Column field="gold_value" header="Gold" sortable>
                        <template #body="prop">
                            <span class="q">{{
                                prop.data.gold_value.toLocaleString()
                            }}</span>
                        </template>
                    </Column>
                </DataTable>

                <template #fallback>
                    <div class="flex justify-center items-center p-4">
                        <i class="pi pi-spin pi-spinner text-2xl"></i>
                    </div>
                </template>
            </Deferred>
        </div>
    </div>
</template>
<style lang="scss">
.tight-column {
    width: 1rem;
}
</style>
