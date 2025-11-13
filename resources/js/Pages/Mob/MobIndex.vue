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
import Slider from "primevue/slider";
import { ref } from "vue";
import dataTablesSortFromProps from "@/Composables/sort-props-for-datatables.js";

import Tipper from "@/Components/Tipper.vue";
import MobTier from "@/Composables/GeneratedEnumObjects/Mobs-MobTier.json";
import MobType from "@/Composables/GeneratedEnumObjects/Mobs-MobType.json";

defineProps({
    table: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const page = usePage();
const loading = ref(false);
const defaultFilters = {
    type: { value: [] },
    tier: { value: [] },
    location: { value: [] },
    level: { value: [0, 200] },
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

    router.get(route("mob.index"), lazyParams.value, {
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
    <Head :title="`Mobs`" />

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
                            :options="jsonObjectToSelectOptions(MobType)"
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
                        Tier:
                        <MultiSelect
                            v-model="lazyParams.filters.tier.value"
                            :options="jsonObjectToSelectOptions(MobTier)"
                            filter
                            option-value="value"
                            option-label="name"
                            placeholder="Select Tier"
                            class="w-full"
                            :show-clear="true"
                        />
                    </label>
                </div>
                <div>
                    <label>
                        Location:
                        <MultiSelect
                            v-model="lazyParams.filters.location.value"
                            :options="[]"
                            filter
                            option-value="value"
                            option-label="name"
                            placeholder="Select Location"
                            class="w-full"
                            :show-clear="true"
                        />
                    </label>
                </div>
                <div>
                    <div class="mb-5 text-center">
                        Level range:
                        {{ lazyParams.filters.level.value[0] }}
                        -
                        {{ lazyParams.filters.level.value[1] }}
                    </div>
                    <Slider
                        v-model="lazyParams.filters.level.value"
                        :min="0"
                        :max="200"
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
                    v-model:multiSortMeta="lazyParams.sort"
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
                    <Column field="id" header="ID" />
                    <Column field="name" header="Name" sortable>
                        <template #body="prop">
                            <Link
                                :href="
                                    route('mob.show', { mob: prop.data.slug })
                                "
                            >
                                <div class="flex">
                                    <div class="mr-1">
                                        <img
                                            v-if="
                                                prop.data.type ===
                                                MobType.Boss.value
                                            "
                                            src="https://cdn-icons-png.flaticon.com/128/2545/2545603.png"
                                            class="w-4 img"
                                            title="boss"
                                        />
                                    </div>
                                    <div
                                        :class="{
                                            'p-message-error':
                                                prop.data.type ===
                                                MobType.Boss.value,
                                        }"
                                    >
                                        {{ prop.data.name }}
                                    </div>
                                    <div class="flex-1"></div>
                                </div>
                            </Link>
                        </template>
                    </Column>
                    <Column field="drops" header="Drops">
                        <template #body="prop">
                            <Button
                                v-if="prop.data.items?.length"
                                icon="pi pi-eye"
                                @click="togglePopover($event, prop.index)"
                            />
                            <Popover :ref="(el) => (popovers[prop.index] = el)">
                                <Fieldset
                                    v-if="prop.data.items?.length"
                                    legend="Drops"
                                >
                                    <template
                                        v-for="item in prop.data.items"
                                        :key="`mob-${prop.data.id}-items-${item.id}`"
                                    >
                                        <div class="mt-2">
                                            <Link
                                                :href="
                                                    route('item.show', {
                                                        item: item.slug,
                                                    })
                                                "
                                            >
                                                <Tipper :data="item"></Tipper>
                                            </Link>
                                        </div>
                                    </template>
                                </Fieldset>
                            </Popover>
                        </template>
                    </Column>
                    <Column field="type" header="Type" sortable>
                        <template #body="prop">
                            {{ jsonObjectGetLabelByValue(MobType, prop.data.type) }}
                        </template>
                    </Column>
                    <Column field="tier" header="Tier" sortable>
                        <template #body="prop">
                            <span class="capitalize">{{
                                prop.data.tier.replace("_", " ")
                            }}</span>
                        </template>
                    </Column>
                    <Column field="level" header="Level" class="tight-column" sortable>
                        <template #body="prop">
                            <span class="capitalize q">
                                {{ prop.data.level }}
                            </span>
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
