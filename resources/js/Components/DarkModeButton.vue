<template>
    <div class="dark-mode-switcher">
        <Chip
            class="dark:bg-surface-800 p-2 cursor-pointer"
            @click="toggleDarkMode"
        >
            <i class="pi" :class="[isDarkMode ? 'pi-sun' : 'pi-moon']" />
        </Chip>
    </div>
</template>

<script setup>
import Chip from "primevue/chip";
import { onMounted } from "vue";

import { useThemeSettings } from "@/Composables/theme-settings";

const { isDarkMode, toggleDarkMode: toggleTheme } = useThemeSettings();

const emit = defineEmits(["isDarkMode"]);

onMounted(() => {
    if (localStorage.getItem("theme")) {
        localStorage.getItem("theme") === "dark" ? setToDark() : setToLight();
    } else if (
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
        setToDark();
    }
});

window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", (e) => {
        if (e.matches) {
            setToDark();
        } else {
            setToLight();
        }
    });

const toggleDarkMode = () => {
    toggleTheme();
    if (!document.documentElement.classList.contains("dark")) {
        setToDark();
    } else {
        setToLight();
    }
};

const setToDark = () => {
    document.documentElement.classList.add("dark");
    localStorage.setItem("theme", "dark");
    emit("isDarkMode", true);
};

const setToLight = () => {
    document.documentElement.classList.remove("dark");
    localStorage.setItem("theme", "light");
    emit("isDarkMode", false);
};
</script>

<style lang="scss" scoped></style>
