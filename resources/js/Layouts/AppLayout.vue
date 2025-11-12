<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import AutoComplete from "@volt/AutoComplete.vue";
import Badge from "primevue/badge";
import Button from "primevue/button";
import Chip from "primevue/chip";
import Dialog from "primevue/dialog";
import Menubar from "primevue/menubar";
import Message from "primevue/message";
import {
    computed,
    nextTick,
    onMounted,
    onUnmounted,
    ref,
    watch,
    watchEffect,
} from "vue";

import ApplicationMark from "@/Components/ApplicationMark.vue";
import DarkModeButton from "@/Components/DarkModeButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Tipper from "@/Components/Tipper.vue";

defineProps({
    title: String,
});

const page = usePage();

const searchModal = ref({
    visible: false,
    position: "top",
    selected: null,
    filtered: [],
    data: {},
});
const autocompleteInput = ref(null);
const search = async (event) => {
    await axios
        .post(route("search.simple"), { search: event.query })
        .then(function (response) {
            if (response.data.typesense.length > 0) {
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

const menuItems = ref([
    // {
    //     label: "Categories",
    //     route: "category.index",
    //     routeGroup: "category.*",
    //     items: [
    //         {
    //             label: "All Categories",
    //             route: "category.index",
    //         },
    //     ],
    // },
    {
        label: "Items",
        route: "item.index",
        routeGroup: "item.*",
    },
]);

onMounted(() => {});

const bannerStyle = ref("success");
const bannerMessage = ref("");

watchEffect(async () => {
    bannerStyle.value = page.props.jetstream.flash?.bannerStyle || "success";
    bannerMessage.value = page.props.jetstream.flash?.banner || "";
});

const logout = () => {
    router.post(route("logout"));
};

const animate = ref(true);
const isLeaving = ref(false);

const theRoute = ref(route());

const currentRoute = computed(() => usePage().props.current_route_salted);
watch(currentRoute, async () => {
    isLeaving.value = true;
    animate.value = false;

    // Wait for leave animation to complete
    await new Promise((resolve) => setTimeout(resolve, 300));

    isLeaving.value = false;
    nextTick(() => {
        animate.value = true;
    });
});

addEventListener("load", () =>
    setTimeout(() => {
        dispatchEvent(new Event("vite:prefetch"));
    }, 3000),
);

// Keyboard shortcut handler for search
const handleGlobalKeydown = (e) => {
    // Only trigger on Cmd+K or Ctrl+K
    if ((e.metaKey || e.ctrlKey) && e.key === "k") {
        e.preventDefault();
        searchModal.value.visible = true;
    }
};

// Add/remove global event listener
onMounted(() => {
    document.addEventListener("keydown", handleGlobalKeydown);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleGlobalKeydown);
});

router.on("navigate", () => {
    theRoute.value = route();
});
const current = computed(() => {
    return theRoute.value.current();
});
</script>

<template>
    <!-- Search -->
    <Dialog
        v-model:visible="searchModal.visible"
        :position="searchModal.position"
        modal
        dismissable-mask
        :style="{ width: '90%', 'max-width': '500px', 'margin-top': '15px' }"
        :show-header="false"
        :pt="{
            content: 'p-0',
            mask: {
                style: 'backdrop-filter: blur(2px)',
            },
        }"
        @show="
            nextTick(() => {
                autocompleteInput.$el.children[0].focus();
            })
        "
    >
        <div class="w-full max-w-xl flex-1">
            <div class="simple-search relative">
                <div class="absolute inset-y-0 z-10 flex items-center pl-2">
                    <i class="pi pi-search text-surface-400" />
                </div>
                <AutoComplete
                    id="mainSearch"
                    ref="autocompleteInput"
                    v-model="searchModal.selected"
                    :suggestions="searchModal.filtered"
                    field="name"
                    placeholder="Search..."
                    class="w-full autocomplete-search py-4"
                    scroll-height="350px"
                    @item-select="searchModal.selected = null"
                    @complete="search($event)"
                >
                    <template #option="slotProps">
                        <div v-if="slotProps.option.slug" class="ml-2 w-full">
                            <div v-if="slotProps.option.type === 'item'">
                                <Link
                                    :href="
                                        route(`${slotProps.option.type}.show`, {
                                            item: `${slotProps.option.slug}`,
                                        })
                                    "
                                    class="whitespace-nowrap"
                                    @click="searchModal.visible = false"
                                >
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
                                                :data="
                                                    searchModal.data.items[
                                                        slotProps.option.id
                                                    ]
                                                "
                                            ></Tipper>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                            <div v-else>
                                {{ slotProps.option.name }}
                            </div>
                            <Link
                                :href="
                                    route(`${slotProps.option.type}.show`, {
                                        item: `${slotProps.option.slug}`,
                                    })
                                "
                                class="absolute inset-0 pointer-events-none"
                                @click="searchModal.visible = false"
                            />
                        </div>
                        <div v-else class="h-full">No Result</div>
                    </template>
                </AutoComplete>
            </div>
        </div>
    </Dialog>
    <!-- /Search -->

    <div>
        <Head :title="title" />

        <div class="min-h-screen transition-all duration-300">
            <nav class="sticky top-0 z-10">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto print:hidden">
                    <Menubar :model="menuItems">
                        <template #start>
                            <Link :href="route('home')">
                                <ApplicationMark />
                            </Link>
                        </template>
                        <template #item="{ item, props, hasSubmenu, root }">
                            <Link
                                v-if="
                                    item.route &&
                                    typeof item.items === 'undefined'
                                "
                                :href="
                                    route(item.route, item.routeObject || null)
                                "
                            >
                                <span
                                    v-ripple
                                    v-bind="props.action"
                                    :class="{
                                        'bg-gray-100 dark:bg-surface-600/80 rounded-lg':
                                            theRoute.current(item.route) &&
                                            root,
                                    }"
                                >
                                    <!-- <span :class="item.icon" /> -->
                                    <span
                                        :class="{ 'ml-2': item.icon?.length }"
                                        >{{ item.label }}</span
                                    >
                                </span>
                            </Link>
                            <a
                                v-else
                                v-ripple
                                class="flex items-center"
                                v-bind="props.action"
                                :class="{
                                    'bg-gray-100 mx-2 dark:bg-surface-600/80 rounded-lg':
                                        theRoute.current(item.routeGroup),
                                }"
                            >
                                <!-- <span :class="item.icon" /> -->
                                <span :class="{ 'ml-2': item.icon?.length }">{{
                                    item.label
                                }}</span>
                                <Badge
                                    v-if="item.badge"
                                    :class="{ 'ml-auto': !root, 'ml-2': root }"
                                    :value="item.badge"
                                />
                                <i
                                    v-if="hasSubmenu"
                                    :class="[
                                        'pi pi-angle-down text-primary-500 dark:text-primary-400-500 dark:text-primary-400',
                                        {
                                            'pi-angle-down ml-2': root,
                                            'pi-angle-right ml-auto': !root,
                                        },
                                    ]"
                                />
                            </a>
                        </template>
                        <template #end>
                            <div class="flex items-center gap-2">
                                <Chip
                                    v-tooltip.bottom="'⌘K'"
                                    class="dark:bg-surface-800 p-2 cursor-pointer"
                                    @click="
                                        searchModal.visible =
                                            !searchModal.visible
                                    "
                                >
                                    <i class="pi pi-search" />
                                </Chip>
                                <DarkModeButton />
                                <Dropdown
                                    v-if="$page.props.auth?.user"
                                    align="right"
                                    width="48"
                                >
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                        >
                                            <img
                                                class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                                    $page.props.auth.user
                                                        .profile_photo_url
                                                "
                                                :alt="
                                                    $page.props.auth.user.name
                                                "
                                            />
                                        </button>

                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-white bg-gray-100 mx-2 dark:bg-surface-600/80 rounded-lg hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.auth?.user?.name
                                                }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Manage Account
                                        </div>

                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink
                                            v-if="
                                                $page.props.jetstream
                                                    .hasApiFeatures
                                            "
                                            :href="route('api-tokens.index')"
                                        >
                                            API Tokens
                                        </DropdownLink>

                                        <div
                                            class="border-t border-gray-200 dark:border-gray-600"
                                        />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                                <!--                                <Link-->
                                <!--                                    v-else-->
                                <!--                                    v-tooltip="'Login'"-->
                                <!--                                    :href="route('login')"-->
                                <!--                                >-->
                                <!--                                    <Chip class="dark:bg-surface-800 p-2">-->
                                <!--                                        <i class="pi pi-user" />-->
                                <!--                                    </Chip>-->
                                <!--                                </Link>-->
                            </div>
                        </template>
                    </Menubar>
                </div>
            </nav>

            <!-- Page Heading -->
            <header id="header-slot" class="bg-white dark:bg-gray-800 shadow">
                <div
                    class="header-wrapper max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
                >
                    <slot name="header" />
                </div>
            </header>

            <!-- Banner -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Message v-if="bannerMessage" :severity="bannerStyle">
                    {{ bannerMessage }}
                </Message>
            </div>

            <transition name="slide-fade" mode="out-in" appear>
                <div
                    v-if="animate"
                    :class="{ 'is-leaving': isLeaving }"
                    class="mx-auto container h-full"
                >
                    <!-- Page Content -->
                    <main>
                        <slot />
                    </main>
                </div>
            </transition>

            <footer>
                <div
                    class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8"
                >
                    <div
                        class="flex justify-center items-center space-x-6 md:order-2 mb-4 lg:mb-0"
                    ></div>
                    <p class="text-center text-sm leading-5 mb-10 lg:mb-0">
                        Blake's Project &copy;
                        {{ new Date().getFullYear() }} All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>
</template>
<style lang="scss">
#mainSearch_list {
    > li {
        overflow: unset;
    }
}

#header-slot > .header-wrapper {
    display: none;
}

#header-slot > .header-wrapper:has(*) {
    display: block;
}

div#mainSearch.autocomplete-search > input {
    padding-left: 45px !important;
    width: 100%;
    @apply py-4 w-full;
}

/*
  Slide Fade Transition
*/
.slide-right-enter-active,
.slide-left-enter-active,
.slide-bottom-enter-active,
.slide-top-enter-active {
    transition: all 0.3s ease-out;
}

.slide-right-leave-active,
.slide-left-leave-active,
.slide-bottom-leave-active,
.slide-top-enter-active {
    transition: all 0.3s;
}

.slide-right-enter-from,
.slide-right-leave-to {
    transform: translateX(50%);
    opacity: 0;
}

.slide-left-enter-from,
.slide-left-leave-to {
    transform: translateX(-50%);
    opacity: 0;
}

.slide-bottom-enter-from,
.slide-bottom-leave-to {
    transform: translateY(25%);
    opacity: 0;
}

.slide-top-enter-from,
.slide-top-leave-to {
    transform: translateY(-50%);
    opacity: 0;
}

/*
  Fade Transition
*/
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/*
  Slide Fade Combined Transition
*/
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-enter-from {
    transform: translateY(10px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}

/* Add appear transition classes */
.slide-fade-appear-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-appear-from {
    transform: translateY(10px);
    opacity: 0;
}

/* Optional: Add class for leaving state */
.is-leaving {
    pointer-events: none;
}
</style>
