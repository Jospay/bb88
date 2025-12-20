<script setup>
import { onMounted, nextTick } from "vue";

onMounted(() => {
    nextTick(() => {
        const sections = document.querySelectorAll("div[id]");
        const mobileLinks = document.querySelectorAll(".mobile-link");
        const navLinks = document.querySelectorAll(".xl\\:flex a");
        const menuBtn = document.getElementById("menu-btn");
        const mobileMenu = document.getElementById("mobile-menu");
        const navbar = document.getElementById("navbar");
        const scrollThreshold = 50;

        // -------------------- NAVBAR BG ON SCROLL --------------------
        function toggleNavbarBackground() {
            if (window.scrollY > scrollThreshold) {
                navbar.classList.add("bg-black");
                navbar.classList.remove("text-white");
            } else {
                navbar.classList.remove("bg-black");
                navbar.classList.add("text-white");
            }
        }

        window.addEventListener("scroll", toggleNavbarBackground);
        toggleNavbarBackground();

        // Utility function to clean href into an ID
        function extractId(href) {
            return href.replace("/", "").replace("#", "");
        }

        // -------------------- MOBILE NAV LINK --------------------
        function activateMobileLink() {
            let scrollY = window.scrollY + 150;
            let currentId = "";

            sections.forEach((section) => {
                const top = section.offsetTop;
                const height = section.offsetHeight;
                if (scrollY >= top && scrollY < top + height) {
                    currentId = section.getAttribute("id");
                }
            });

            mobileLinks.forEach((link) => {
                const href = extractId(link.getAttribute("href"));
                link.classList.remove("active");
                if (href === currentId) link.classList.add("active");
            });
        }

        mobileLinks.forEach((link) => {
            link.addEventListener("click", function (e) {
                const rawHref = this.getAttribute("href");
                const targetId = extractId(rawHref);

                // Check if user is already on the Home page
                const isHomePage = window.location.pathname === "/";

                if (!isHomePage) {
                    // Allow normal navigation to /#section
                    return;
                }

                // Already on home page → smooth scroll
                e.preventDefault();

                const target = document.getElementById(targetId);

                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 70,
                        behavior: "smooth",
                    });
                }

                mobileMenu.classList.add("hidden");
            });
        });

        window.addEventListener("scroll", activateMobileLink);
        activateMobileLink();

        // -------------------- DESKTOP NAV LINK --------------------
        function activateLink() {
            let scrollY = window.scrollY + 150;
            let currentId = "";

            sections.forEach((section) => {
                const top = section.offsetTop;
                const height = section.offsetHeight;
                if (scrollY >= top && scrollY < top + height) {
                    currentId = section.getAttribute("id");
                }
            });

            navLinks.forEach((link) => {
                const underline = link.querySelector("span span");
                const href = extractId(link.getAttribute("href"));

                link.classList.remove("text-brand-blue");
                link.classList.add("text-white");
                underline?.classList.remove("w-full");

                if (href === currentId) {
                    link.classList.add("text-brand-blue");
                    link.classList.remove("text-white");
                    underline?.classList.add("w-full");
                }
            });
        }

        window.addEventListener("scroll", activateLink);
        activateLink();

        // -------------------- MOBILE MENU TOGGLE --------------------
        menuBtn?.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
        });
    });
});
</script>

<template>
    <nav
        id="navbar"
        class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[1320px] z-50 xl:mt-5 xl:rounded-lg transition-colors duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="shrink-0">
                    <img src="@/assets/nav logo.png" class="h-10" alt="" />
                </div>

                <!-- Desktop Nav Links -->
                <div class="hidden xl:flex items-center space-x-8">
                    <a
                        href="/#"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            Home
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a
                        href="/#aboutus"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            About Us
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a
                        href="/#services"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            Services
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a
                        href="/#technology"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            Technology
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <!-- <a
                        href="/#portfolio"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            Portfolio
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a> -->

                    <a
                        href="/#events"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            2026 Events
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a
                        href="/#careers"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            Careers
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a
                        href="/#new"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            New & Insights
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a
                        href="/#contact"
                        class="relative hover:text-brand-blue font-medium text-white group"
                    >
                        <span class="inline-block relative">
                            Contact Us
                            <span
                                class="absolute bottom-0.5 left-0 w-0 h-0.5 bg-brand-blue transition-all duration-300 group-hover:w-full"
                            ></span>
                        </span>
                    </a>

                    <a href="/join">
                        <button
                            class="bg-brand-blue text-white px-5 py-1 rounded"
                        >
                            Join 2026 Event
                        </button>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="xl:hidden">
                    <button
                        id="menu-btn"
                        class="text-brand-blue hover:text-brand-blue focus:outline-none"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            id="mobile-menu"
            class="hidden xl:hidden bg-black border-t border-gray-200 py-3"
        >
            <a
                href="/#home"
                class="mobile-link block px-4 py-2 text-brand-blue hover:text-gray-100 hover:bg-brand-green"
                >Home</a
            >
            <a
                href="/#aboutus"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >About</a
            >
            <a
                href="/#services"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >Services</a
            >
            <a
                href="/#technology"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >Technology</a
            >
            <a
                href="/#portfolio"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >Portfolio</a
            >
            <a
                href="/#careers"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >Careers</a
            >
            <a
                href="/#new"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >New & Insights</a
            >
            <a
                href="/#contact"
                class="mobile-link block px-4 py-2 text-brand-blue hover:bg-brand-green"
                >Contact Us</a
            >

            <a href="/join">
                <button
                    class="bg-brand-blue text-white px-5 py-1 rounded ms-3 mt-3 mb-2"
                >
                    Join 2026 Event
                </button>
            </a>
        </div>
    </nav>
</template>

<style scoped>
.mobile-link.active {
    background-color: #00b900 !important;
    color: white !important;
}
</style>
