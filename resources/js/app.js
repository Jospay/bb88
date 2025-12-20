import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import DashboardLayout from "./layouts/DashboardLayout.vue"; // Make sure the path matches your folder

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./pages/**/*.vue", { eager: true });
        let page = pages[`./pages/${name}.vue`].default;

        // Automatically wrap pages in the dashboard folder with the layout
        if (name.startsWith("dashboard/")) {
            page.layout = page.layout || DashboardLayout;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
