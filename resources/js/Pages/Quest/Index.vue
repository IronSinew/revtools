<script setup>
import { Deferred, Link, router, usePage } from "@inertiajs/vue3";
import Drawer from "@volt/Drawer.vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Fieldset from "primevue/fieldset";
import Message from "primevue/message";
import MultiSelect from "primevue/multiselect";
import Popover from "primevue/popover";
import Select from "primevue/select";
import Slider from "primevue/slider";
import { ref } from "vue";

import Tipper from "@/Components/Tipper.vue";
import ClassType from "@/Composables/GeneratedEnumObjects/ClassType.json";
import QuestRewardType from "@/Composables/GeneratedEnumObjects/Quests-QuestRewardType.json";

const props = defineProps({
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
    class: { value: null },
    reward_type: { value: [] },
    level: { value: [0, 100] },
};

const lazyParams = ref({
    page: parseInt(page.props.params?.page || 0),
    last: parseInt(page.props.params?.last || 0),
    rows: parseInt(page.props.params?.rows || 25),
    sort: page.props.params?.sort || [],
    filters:
        page.props.params?.filters ||
        JSON.parse(JSON.stringify(defaultFilters)),
});

const clearFilters = () => {
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

    router.get(route("quests.index"), lazyParams.value, {
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
    <Head :title="`Quests`" />

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
                        Reward Type:
                        <MultiSelect
                            v-model="lazyParams.filters.reward_type.value"
                            :options="
                                jsonObjectToSelectOptions(QuestRewardType)
                            "
                            filter
                            option-value="value"
                            option-label="name"
                            placeholder="Select Reward Type"
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
                        {{ lazyParams.filters.level.value[0] }}
                        -
                        {{ lazyParams.filters.level.value[1] }}
                    </div>
                    <Slider
                        v-model="lazyParams.filters.level.value"
                        :min="0"
                        :max="100"
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
                        field="name"
                        header="Name"
                        class="tight-column"
                        sortable
                    />
                    <Column
                        field="level"
                        header="Level"
                        class="tight-column"
                        sortable
                    />
                    <Column
                        field="required_class"
                        header="Required Class"
                        class="tight-column"
                    >
                        <template #body="prop">
                            <span
                                v-for="required_class in new Set(
                                    prop.data.required_class,
                                )"
                                :key="required_class"
                                class="capitalize"
                            >
                                {{ required_class + " " }}
                            </span>
                        </template>
                    </Column>
                    <Column
                        field="quest_giver"
                        header="Quest Giver"
                        class="tight-column"
                        sortable
                    />
                    <Column field="raw_rewards" header="Rewards">
                        <template #body="prop">
                            <div class="text-center">
                                <Button
                                    v-if="prop.data.raw_rewards?.length"
                                    icon="pi pi-eye"
                                    @click="togglePopover($event, prop.index)"
                                />
                                <Popover
                                    :ref="(el) => (popovers[prop.index] = el)"
                                >
                                    <Fieldset
                                        v-if="prop.data.raw_rewards?.length"
                                        legend="Quest Rewards"
                                    >
                                        <template
                                            v-for="item in prop.data.items"
                                            :key="item.id"
                                        >
                                            <Link
                                                :href="
                                                    route('item.show', {
                                                        item: item.slug,
                                                    })
                                                "
                                            >
                                                <Tipper :data="item"></Tipper>
                                            </Link>
                                        </template>
                                        <template
                                            v-for="reward in prop.data
                                                .raw_rewards"
                                            :key="`item-${prop.data.slug}-reward-${reward.name}`"
                                        >
                                            <div
                                                class="mt-2"
                                                :class="{
                                                    'text-green-500':
                                                        reward.type ===
                                                            QuestRewardType
                                                                .Faction
                                                                .value ||
                                                        reward.type ===
                                                            QuestRewardType
                                                                .SkillPoint
                                                                .value ||
                                                        reward.type ===
                                                            QuestRewardType
                                                                .SpellPoint
                                                                .value,
                                                    'text-amber-500':
                                                        reward.type ===
                                                        QuestRewardType.Title
                                                            .value,
                                                }"
                                            >
                                                <span
                                                    >{{
                                                        reward.name.replaceAll(
                                                            "_",
                                                            " ",
                                                        )
                                                    }}
                                                </span>
                                                <span
                                                    v-if="
                                                        reward.amount > 1 &&
                                                        reward.type !==
                                                            QuestRewardType
                                                                .Title.value
                                                    "
                                                    class="ml-2"
                                                    >{{ reward.amount }}</span
                                                >
                                            </div>
                                        </template>
                                    </Fieldset>
                                </Popover>
                            </div>
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

<style scoped lang="scss"></style>
