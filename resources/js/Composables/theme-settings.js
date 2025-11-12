import { ref } from "vue";

const isDarkMode = ref(localStorage.getItem("theme") === "dark");

export function useThemeSettings() {
    const toggleDarkMode = () => (isDarkMode.value = !isDarkMode.value);

    return { isDarkMode, toggleDarkMode };
}
