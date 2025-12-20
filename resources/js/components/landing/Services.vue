<script setup>
import { onMounted } from "vue";

onMounted(() => {
    // -------------------- FILTERING --------------------
    const filterNav = document.getElementById("filter-nav");
    const serviceItems = document.querySelectorAll(".service-item");
    const filterButtons = document.querySelectorAll(".filter-btn");

    function filterServices(category) {
        serviceItems.forEach((item) => {
            const itemCategory = item.getAttribute("data-category");

            if (itemCategory === category) {
                item.classList.remove("hidden-item");
            } else {
                item.classList.add("hidden-item");
            }
        });

        filterButtons.forEach((btn) => {
            const isActive = btn.getAttribute("data-filter") === category;
            btn.classList.toggle("text-brand-blue", isActive);
            btn.classList.toggle("border-brand-blue", isActive);
            btn.classList.toggle("border-transparent", !isActive);
        });
    }

    filterNav.addEventListener("click", (event) => {
        const target = event.target.closest(".filter-btn");
        if (target) {
            const category = target.getAttribute("data-filter");
            filterServices(category);
        }
    });

    document.querySelectorAll("a[data-filter]").forEach((link) => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            const category = link.getAttribute("data-filter");
            filterServices(category);
            document.querySelector("#services")?.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        });
    });

    filterServices("development");
});
</script>

<template>
    <!-- Start Services -->
    <div
        id="services"
        class="bg-[url('@/assets/nbg3.jpg')] bg-cover bg-no-repeat pt-8 pb-12 scroll-mt-17"
    >
        <div
            class="text-center bg-[url('@/assets/ser1.jpg')] bg-center bg-cover bg-no-repeat text-white py-8"
        >
            <h1 class="text-white font-bold text-4xl">SERVICES</h1>
            <p class="text-lg/[22px] pt-3 px-5 sm:block hidden">
                We deliver full-cycle digital entertainment services, from
                ideation to post-launch success, helping brands, <br />
                creators, and players bring stories to life through innovation.
            </p>

            <p class="text-md/[19px] pt-3 px-5 sm:hidden block">
                We deliver full-cycle digital entertainment services, from
                ideation to post-launch success, helping brands, creators, and
                players bring stories to life through innovation.
            </p>
        </div>

        <div class="w-full max-w-[1320px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Category Filter Buttons -->
            <div
                id="filter-nav"
                class="text-white flex flex-col sm:flex-row sm:px-0 px-3 justify-evenly pt-8 pb-5 font-bold text-base md:text-lg space-y-4 sm:space-y-0"
            >
                <!-- Start filter development -->
                <button
                    data-filter="development"
                    class="filter-btn py-2 px-3 sm:px-6 transition duration-300 ease-in-out border-b-4 border-brand-blue text-brand-blue"
                >
                    GAME DEVELOPMENT & DESIGN
                </button>
                <!-- End filter development -->

                <!-- Start filter marketing -->
                <button
                    data-filter="marketing"
                    class="filter-btn py-2 px-3 sm:px-6 transition duration-300 ease-in-out border-b-4 border-transparent hover:border-brand-blue"
                >
                    GAME MARKETING & GROWTH
                </button>
                <!-- End filter marketing -->

                <!-- Start filter post-launch -->
                <button
                    data-filter="post-launch"
                    class="filter-btn py-2 px-3 sm:px-6 transition duration-300 ease-in-out border-b-4 border-transparent hover:border-brand-blue"
                >
                    POST-LAUNCH & LIVE SERVICES
                </button>
                <!-- End filter post-launch -->
            </div>

            <!-- Service Content Container -->
            <div id="service-content" class="mt-8">
                <!-- Start Development Items (Initial active category) -->
                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90"
                >
                    <strong class="px-6 block">Full-Cycle Development:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >From concept to live release and updates.</span
                    >
                </div>

                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90"
                >
                    <strong class="px-6 block"
                        >Multi-Platform Expertise:</strong
                    >
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >PC, mobile, console, and web games.</span
                    >
                </div>

                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">AR/VR/MR Experiences:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Immersive gameplay beyond traditional screens.</span
                    >
                </div>

                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Next-Gen Integration:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >AI, blockchain, and machine learning for smarter
                        gaming.</span
                    >
                </div>

                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Game Art & Assets:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Concept art, 2D/3D models, and animation.</span
                    >
                </div>

                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">UI/UX Design:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Seamless, intuitive user experiences.</span
                    >
                </div>

                <div
                    data-category="development"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Prototyping:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Early-stage playable builds to test concepts and
                        mechanics.</span
                    >
                </div>
                <!-- End Development Items (Initial active category) -->

                <!-- Start Marketing Items Items (Initial active category) -->
                <div
                    data-category="marketing"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out cursor-pointer hover:bg-opacity-90"
                >
                    <strong class="px-6 block"
                        >Digital Marketing Strategy:</strong
                    >
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Full campaign planning from teaser to release.</span
                    >
                </div>

                <div
                    data-category="marketing"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90"
                >
                    <strong class="px-6 block"
                        >App Store Optimization (ASO):</strong
                    >
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Boosting visibility and downloads.</span
                    >
                </div>

                <div
                    data-category="marketing"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block"
                        >Influencer & Esports Marketing:</strong
                    >
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Authentic community-driven engagement.</span
                    >
                </div>

                <div
                    data-category="marketing"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Paid User Acquisition:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Precision targeting for player growth.</span
                    >
                </div>

                <div
                    data-category="marketing"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Content Marketing:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Trailers, developer diaries, and storytelling.</span
                    >
                </div>

                <div
                    data-category="marketing"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Esports Integration: </strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Competitive events and tournament sponsorships.</span
                    >
                </div>
                <!-- End Marketing Items Items (Initial active category) -->

                <!-- Start Post-Launch Items (Initial active category) -->
                <div
                    data-category="post-launch"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out cursor-pointer hover:bg-opacity-90"
                >
                    <strong class="px-6 block">LiveOps & Monetization:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Managing in-game events and updates for
                        retention.</span
                    >
                </div>

                <div
                    data-category="post-launch"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90"
                >
                    <strong class="px-6 block">Player Support:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >24/7 multilingual customer service.</span
                    >
                </div>

                <div
                    data-category="post-launch"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Community Management:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Immersive gameplay beyond traditional screens.</span
                    >
                </div>

                <div
                    data-category="post-launch"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">QA & Testing:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Ensuring quality and performance across
                        platforms.</span
                    >
                </div>

                <div
                    data-category="post-launch"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Game Analytics:</strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Real-time insights for better gameplay and
                        monetization.</span
                    >
                </div>

                <div
                    data-category="post-launch"
                    class="service-item bg-brand-blue text-white w-full py-4 text-lg rounded-lg shadow-xl transition-all duration-500 ease-in-out mt-4 cursor-pointer hover:bg-opacity-90 hidden-item"
                >
                    <strong class="px-6 block">Localization: </strong>
                    <span class="px-6 block font-normal text-sm opacity-90"
                        >Global-ready games adapted for every culture and
                        language.</span
                    >
                </div>
                <!-- End Post-Launch Items (Initial active category) -->
            </div>
        </div>
    </div>
    <!-- End Services -->
</template>

<style scoped>
.service-item {
    max-height: 200px;
    overflow: hidden;
}
.hidden-item {
    max-height: 0;
    margin-top: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    opacity: 0;
}
</style>
