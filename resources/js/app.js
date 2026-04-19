import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import DashboardLayout from "./layouts/DashboardLayout.vue";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });

        // Try the exact match first
        let page = pages[`./Pages/${name}.vue`];

        // If not found, do a case-insensitive search through the keys
        if (!page) {
            const pathArray = Object.keys(pages);
            const matchingPath = pathArray.find(
                (path) =>
                    path.toLowerCase() === `./pages/${name.toLowerCase()}.vue`,
            );
            if (matchingPath) {
                page = pages[matchingPath];
            }
        }

        if (!page) {
            throw new Error(
                `Inertia Page [${name}] not found at ./Pages/${name}.vue`,
            );
        }

        const result = page.default;

        // Apply layout for Dashboard and Player pages
        if (name.startsWith("dashboard/") || name.startsWith("player/")) {
            result.layout = result.layout || DashboardLayout;
        }

        return result;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
