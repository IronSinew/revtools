import './bootstrap'
import '../css/app.css'
import 'primeicons/primeicons.css'

import {createInertiaApp, Head, Link} from '@inertiajs/vue3'
import BadgeDirective from 'primevue/badgedirective'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import Ripple from 'primevue/ripple'
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip'
import {createApp, h } from 'vue'
import VueTippy from 'vue-tippy'
import { ZiggyVue } from 'ziggy-js'

import AppLayout from "@/Layouts/AppLayout.vue";

const jsonObjectToSelectOptions = (object) => {
    return _.filter(_.sortBy(Object.values(object), ["label"]), function (row) {
        return (
            typeof row.is_public === "undefined" ||
            (typeof row.is_public !== "undefined" && row.is_public)
        );
    });
};

/**
 * Standardized function to get an enum json object's label by its value
 *
 * @param {Object} jsonObject
 * @param {string} jsonKey
 * @param {string} defaultIfNotFound
 * @returns {string}
 */
const jsonObjectGetLabelByValue = (jsonObject, jsonKey, defaultIfNotFound) => {
    const missingResponse = defaultIfNotFound || "Unknown";
    return _.find(jsonObject, { value: jsonKey }).label || missingResponse;
};

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const pageModules = import.meta.glob("./Pages/**/*.vue");
        const page = (await pageModules[`./Pages/${name}.vue`]()).default;

        page.layout = AppLayout;

        return page;
    },
    setup ({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ToastService)
            .use(VueTippy)
            .use(PrimeVue, {
                theme: "none",
                ripple: true
            })
            .component("Head", Head)
            .component("Link", Link)
            .directive('ripple', Ripple)
            .directive('badge', BadgeDirective)
            .directive('tooltip', Tooltip)
            .use(ConfirmationService)
            .mixin({
                methods: {
                    jsonObjectToSelectOptions: jsonObjectToSelectOptions,
                    jsonObjectGetLabelByValue: jsonObjectGetLabelByValue,
                },
            })
            .mount(el)
    },
    progress: {
        color: '#fb923c',
        delay: 200,
        showSpinner: true
    }
})
