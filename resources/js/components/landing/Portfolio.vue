<script setup>
import { onMounted } from "vue";

onMounted(() => {
    if (typeof $ === "undefined") {
        console.error("jQuery not loaded");
        return;
    }

    // Initialize Owl Carousel
    if ($(".owl-2").length > 0) {
        var owl2 = $(".owl-2").owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 20,
            smartSpeed: 1000,
            autoplay: false,
            nav: false,
            dots: false,
            pauseOnHover: false,
            responsive: {
                850: {
                    margin: 20,
                    nav: false,
                    items: 1,
                },
                1000: {
                    margin: 20,
                    stagePadding: 0,
                    nav: false,
                    items: 1,
                },
                1280: {
                    margin: 20,
                    stagePadding: 0,
                    nav: false,
                    items: 2,
                },
            },
        });

        // Custom navigation buttons
        $(".bg-left-half123").click(() => {
            owl2.trigger("prev.owl.carousel", [1000]);
        });

        $(".bg-right-half").click(() => {
            owl2.trigger("next.owl.carousel", [1000]);
        });
    }
});
</script>

<template>
    <!-- Start Portfolio -->
    <div
        id="portfolio"
        class="bg-[url('@/assets/nbg5.jpg')] bg-cover bg-no-repeat py-10 scroll-mt-20"
    >
        <div class="w-full max-w-[1320px] px-5 mx-auto">
            <h1 class="text-white font-bold text-4xl text-center">PORTFOLIO</h1>
            <div
                class="sm:w-[500px] bg-gradient-to-r from-blue-500 to-red-500 mt-5 text-center mx-auto"
            >
                <h1 class="text-white font-bold py-2 text-2xl">
                    Games & Projects
                </h1>
            </div>

            <div class="mt-10">
                <div class="relative">
                    <!-- slide button start -->
                    <div
                        class="absolute top-1/2 start-4 -translate-y-1/2"
                        style="z-index: 5"
                    >
                        <button
                            class="bg-brand-blue text-white bg-left-half123 rounded-[50%] h-[40px] w-[40px] border-2 border-white opacity-50"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    </div>

                    <div
                        class="absolute top-1/2 end-4 -translate-y-1/2"
                        style="z-index: 5"
                    >
                        <button
                            class="bg-brand-blue text-white bg-right-half rounded-[50%] h-[40px] w-[40px] border-2 border-white opacity-50"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                    <!-- slide button start -->

                    <!-- Start Carasousel -->
                    <div class="owl-carousel owl-2">
                        <div class="item">
                            <div
                                class="grid grid-cols-12 border-2 border-brand-red items-center rounded-xl p-3 bg-gradient-to-r from-transparent via-brand-blue/90 to-brand-blue"
                            >
                                <div
                                    class="sm:col-span-5 col-span-12 xl:pe-0 md:pe-8 pe-0 sm:mb-0 mb-4"
                                >
                                    <img
                                        src="@/assets/game.jpg"
                                        class="rounded-xl"
                                        alt=""
                                    />
                                </div>

                                <div
                                    class="sm:col-span-7 col-span-12 sm:px-3 text-white"
                                >
                                    <h1
                                        class="font-bold xl:text-lg/[20px] md:text-2xl/[25px] text-lg/[20px]"
                                    >
                                        Game Title: Myth Realms: Rise of the
                                        Diwatas
                                    </h1>
                                    <div
                                        class="xl:text-sm/[20px] md:text-xl/[25px] text-sm/[20px]"
                                    >
                                        <p class="mt-3">
                                            Genre: Action-Adventure RPG
                                        </p>
                                        <p>
                                            Platform: PC, Mobile (iOS & Android)
                                        </p>
                                        <p>
                                            Technology: Unity Engine, Blockchain
                                            Integration, AR Companion App
                                        </p>

                                        <p class="font-bold pt-3">Overview</p>
                                        <p>
                                            Myth Realms: Rise of the Diwatas is
                                            a Filipino <br />
                                            myth-inspired action-adventure RPG
                                            that reimagines..
                                        </p>
                                    </div>

                                    <div
                                        id="flex-button"
                                        class="flex flex-wrap gap-3 justify-between items-center xl:pt-4 md:pt-8 pt-4"
                                    >
                                        <div>
                                            <h1
                                                class="xl:text-2xl md:text-3xl text-2xl font-bold"
                                            >
                                                ₱199
                                            </h1>
                                        </div>

                                        <div
                                            id="read-button"
                                            class="flex gap-3"
                                        >
                                            <div>
                                                <button
                                                    class="bg-white text-brand-blue font-bold px-2 py-1 rounded"
                                                >
                                                    Read More
                                                </button>
                                            </div>

                                            <div>
                                                <button
                                                    class="bg-brand-green text-white font-bold px-5 py-1 rounded"
                                                >
                                                    <i
                                                        class="fa-solid fa-cart-shopping"
                                                    ></i>
                                                    Buy
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="read-button-mobile">
                                        <div
                                            class="flex justify-end pt-2 gap-3"
                                        >
                                            <div>
                                                <button
                                                    class="bg-white text-brand-blue font-bold px-2 py-1 rounded"
                                                >
                                                    Read More
                                                </button>
                                            </div>

                                            <div>
                                                <button
                                                    class="bg-brand-green text-white font-bold px-5 py-1 rounded"
                                                >
                                                    <i
                                                        class="fa-solid fa-cart-shopping"
                                                    ></i>
                                                    Buy
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="item">
                            <div
                                class="grid grid-cols-12 border-2 border-brand-red items-center rounded-xl p-3 bg-gradient-to-r from-transparent via-brand-blue/90 to-brand-blue"
                            >
                                <div
                                    class="sm:col-span-5 col-span-12 xl:pe-0 md:pe-8 pe-0 sm:mb-0 mb-4"
                                >
                                    <img
                                        src="@/assets/game.jpg"
                                        class="rounded-xl"
                                        alt=""
                                    />
                                </div>

                                <div
                                    class="sm:col-span-7 col-span-12 sm:px-3 text-white"
                                >
                                    <h1
                                        class="font-bold xl:text-lg/[20px] md:text-2xl/[25px] text-lg/[20px]"
                                    >
                                        Game Title: Myth Realms: Rise of the
                                        Diwatas
                                    </h1>
                                    <div
                                        class="xl:text-sm/[20px] md:text-xl/[25px] text-sm/[20px]"
                                    >
                                        <p class="mt-3">
                                            Genre: Action-Adventure RPG
                                        </p>
                                        <p>
                                            Platform: PC, Mobile (iOS & Android)
                                        </p>
                                        <p>
                                            Technology: Unity Engine, Blockchain
                                            Integration, AR Companion App
                                        </p>

                                        <p class="font-bold pt-3">Overview</p>
                                        <p>
                                            Myth Realms: Rise of the Diwatas is
                                            a Filipino <br />
                                            myth-inspired action-adventure RPG
                                            that reimagines..
                                        </p>
                                    </div>

                                    <div
                                        id="flex-button"
                                        class="flex flex-wrap gap-3 justify-between items-center xl:pt-4 md:pt-8 pt-4"
                                    >
                                        <div>
                                            <h1
                                                class="xl:text-2xl md:text-3xl text-2xl font-bold"
                                            >
                                                ₱199
                                            </h1>
                                        </div>

                                        <div
                                            id="read-button"
                                            class="flex gap-3"
                                        >
                                            <div>
                                                <button
                                                    class="bg-white text-brand-blue font-bold px-2 py-1 rounded"
                                                >
                                                    Read More
                                                </button>
                                            </div>

                                            <div>
                                                <button
                                                    class="bg-brand-green text-white font-bold px-5 py-1 rounded"
                                                >
                                                    <i
                                                        class="fa-solid fa-cart-shopping"
                                                    ></i>
                                                    Buy
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="read-button-mobile">
                                        <div
                                            class="flex justify-end pt-2 gap-3"
                                        >
                                            <div>
                                                <button
                                                    class="bg-white text-brand-blue font-bold px-2 py-1 rounded"
                                                >
                                                    Read More
                                                </button>
                                            </div>

                                            <div>
                                                <button
                                                    class="bg-brand-green text-white font-bold px-5 py-1 rounded"
                                                >
                                                    <i
                                                        class="fa-solid fa-cart-shopping"
                                                    ></i>
                                                    Buy
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Carasousel -->
                </div>

                <!-- Start view more -->
                <div class="text-center pt-9">
                    <button
                        class="bg-brand-blue px-20 text-white font-bold text-2xl rounded-xl py-2"
                    >
                        View more
                    </button>
                </div>
                <!-- Endview more -->
            </div>
        </div>
    </div>
    <!-- End Portfolio -->
</template>

<style scoped>
#read-button-mobile {
    display: none;
}

@media only screen and (max-width: 400px) {
    .flex-button {
        display: grid !important;
        justify-content: none !important;
        padding-top: 5px !important;
    }
    .read-button {
        display: none !important;
    }
    .read-button-mobile {
        display: block !important;
    }
}
</style>
