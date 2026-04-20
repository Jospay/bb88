<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";

const page = usePage();

// desktop = width control
const isSidebarOpen = ref(true);

// mobile overlay control
const isMobileSidebarOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value; // desktop
};

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

const isAdmin = computed(() => !!page.props.auth.admin);

const confirmLogout = () => {
    const url = isAdmin.value ? "/admin/logout" : "/player/logout";

    router.post(url, {
        onSuccess: () => router.visit("/login"),
    });
};

const managementLinks = [
    { name: "Admin Dashboard", href: "/admin", icon: "fa-gauge" },
    { name: "Users Management", href: "/admin/users", icon: "fa-user" },
    { name: "Earning Management", href: "/admin/earning", icon: "fa-coins" },
    {
        name: "Allocation Management",
        href: "/admin/allocation",
        icon: "fa-peso-sign",
    },
];

const playerLinks = [
    { name: "Player Dashboard", href: "/player", icon: "fa-gauge" },
];

const activeLinks = computed(() => {
    return isAdmin.value ? managementLinks : playerLinks;
});
</script>

<template>
    <div class="h-screen flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header
            class="fixed top-0 left-0 right-0 h-[60px] bg-brand-dark-black border-b-2 border-brand-border-black z-50"
        >
            <div class="flex items-center justify-between sm:pr-8 pr-7d h-full">
                <div class="flex items-center gap-5">
                    <div class="lg:hidden">
                        <!-- MOBILE TOGGLE -->
                        <i
                            @click="toggleMobileSidebar"
                            class="fa-solid fa-bars text-white py-4 px-5 cursor-pointer"
                        ></i>
                    </div>
                    <div class="hidden lg:block">
                        <!-- DESKTOP TOGGLE -->
                        <i
                            @click="toggleSidebar"
                            class="fa-solid fa-bars text-white py-4 px-5 border-e-2 border-brand-border-black cursor-pointer hover:bg-gray-800 transition"
                        ></i>
                    </div>

                    <img src="@/assets/footer logo.png" class="h-7" />
                </div>

                <Link
                    :href="isAdmin ? '/admin/profile' : '/player/profile'"
                    class="pr-5"
                >
                    <button
                        class="text-white bg-brand-blue/20 border border-brand-blue/50 px-4 py-1 rounded-md hover:bg-brand-blue/30 transition"
                    >
                        Profile
                    </button>
                </Link>
            </div>
        </header>

        <!-- BODY -->
        <div class="flex pt-[60px] h-screen overflow-hidden">
            <!-- ================= DESKTOP SIDEBAR ================= -->
            <aside
                class="hidden lg:flex flex-col bg-brand-dark-black border-e-2 border-brand-border-black transition-all duration-300 overflow-hidden"
                :class="isSidebarOpen ? 'w-[300px]' : 'w-0'"
            >
                <div class="w-[300px] h-full flex flex-col text-white">
                    <div
                        class="p-5 flex-1 overflow-y-auto flex flex-col justify-between"
                    >
                        <div class="grid gap-1">
                            <Link
                                v-for="link in activeLinks"
                                :key="link.href"
                                :href="link.href"
                            >
                                <button
                                    class="flex gap-3 py-2 px-3 rounded-xl w-full hover:bg-brand-light-black transition"
                                >
                                    <i :class="['fa-solid', link.icon]"></i>
                                    {{ link.name }}
                                </button>
                            </Link>
                        </div>

                        <div
                            class="mt-auto pt-5 border-t border-brand-border-black"
                        >
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <button
                                        class="flex gap-3 py-2 px-3 rounded-xl w-full text-red-500 hover:bg-red-500/10 transition"
                                    >
                                        <i
                                            class="fa-solid fa-right-from-bracket"
                                        ></i>
                                        Logout
                                    </button>
                                </AlertDialogTrigger>
                                <AlertDialogContent
                                    class="bg-brand-dark-black text-white border-brand-border-black"
                                >
                                    <AlertDialogHeader>
                                        <AlertDialogTitle
                                            >Are you sure you want to
                                            logout?</AlertDialogTitle
                                        >
                                        <AlertDialogDescription
                                            class="text-gray-400"
                                        >
                                            This will end your current session.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            class="bg-transparent border-white/20 text-white hover:bg-white/10"
                                            >Cancel</AlertDialogCancel
                                        >
                                        <AlertDialogAction
                                            @click="confirmLogout"
                                            class="bg-red-600 hover:bg-red-700 text-white border-none"
                                        >
                                            Logout
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ================= MOBILE OVERLAY SIDEBAR ================= -->
            <div
                v-if="isMobileSidebarOpen"
                class="fixed inset-0 bg-black/50 z-40 lg:hidden"
                @click="isMobileSidebarOpen = false"
            ></div>

            <aside
                class="fixed top-[60px] left-0 h-[calc(100%-60px)] w-[300px] bg-brand-dark-black border-e-2 border-brand-border-black z-50 lg:hidden transition-transform duration-300"
                :class="
                    isMobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'
                "
            >
                <div
                    class="h-full flex flex-col text-white p-5 overflow-y-auto"
                >
                    <p
                        class="text-[10px] text-brand-blue font-black uppercase tracking-[3px] mb-2"
                    >
                        {{ isAdmin ? "Administrator" : "Player Panel" }}
                    </p>

                    <div class="grid gap-1">
                        <Link
                            v-for="link in activeLinks"
                            :key="link.href"
                            :href="link.href"
                        >
                            <button
                                class="flex gap-3 py-2 px-3 rounded-xl w-full hover:bg-brand-light-black"
                            >
                                <i :class="['fa-solid', link.icon]"></i>
                                {{ link.name }}
                            </button>
                        </Link>
                    </div>
                </div>
            </aside>

            <!-- MAIN (desktop pushes, mobile full width) -->
            <main class="flex-1 bg-brand-light-black overflow-y-auto p-6">
                <slot />
            </main>
        </div>

        <!-- LOGOUT MODAL -->
        <AlertDialog>
            <AlertDialogContent class="bg-brand-dark-black text-white">
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                </AlertDialogHeader>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
