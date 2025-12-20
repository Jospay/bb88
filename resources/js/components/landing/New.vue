<script setup>
import { onMounted } from "vue";

onMounted(() => {
    if (typeof $ === "undefined") {
        console.error("jQuery not loaded.");
        return;
    }

    // Initialize Owl Carousel (owl-0)
    if ($(".owl-0").length > 0) {
        const owl0 = $(".owl-0").owlCarousel({
            center: true,
            items: 3,
            loop: true,
            stagePadding: 100,
            margin: 20,
            smartSpeed: 1000,
            autoplay: false,
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1,
                    stagePadding: 50,
                },
                600: {
                    items: 3,
                    stagePadding: 80,
                },
                1200: {
                    items: 3,
                    stagePadding: 150,
                },
            },
        });

        // Navigation Buttons
        $(".bg-left-owl0").click(() => {
            owl0.trigger("prev.owl.carousel", [1000]);
        });

        $(".bg-right-owl0").click(() => {
            owl0.trigger("next.owl.carousel", [1000]);
        });

        // Highlight center item
        owl0.on("changed.owl.carousel", function (event) {
            $(".owl-item .item-content-wrap").removeClass("active-center");

            const index = event.item.index;
            $(".owl-item")
                .eq(index)
                .find(".item-content-wrap")
                .addClass("active-center");
        });

        // Trigger once on load
        setTimeout(() => {
            $(".owl-item.center .item-content-wrap").addClass("active-center");
        }, 50);
    }
});
</script>

<template>
    <!-- Start New -->
    <div
        id="new"
        class="bg-[url('@/assets/bg7.jpg')] bg-center bg-cover bg-no-repeat pt-10 pb-14 scroll-mt-23"
    >
        <div class="w-full max-w-[1320px] mx-auto pt-5">
            <div class="relative py-2 lg:block hidden">
                <h1
                    class="text-white font-bold text-4xl absolute start-[9%] bottom-[-8px]"
                >
                    NEWS & INSIGHTS
                </h1>
            </div>

            <div
                class="text-center lg:bg-[url('@/assets/nandi.png')] bg-[url('@/assets/news.jpg')] bg-center bg-cover bg-no-repeat text-white py-8"
            >
                <h1
                    class="text-white font-bold text-4xl pt-4 pb-3 lg:hidden block"
                >
                    NEWS & INSIGHTS
                </h1>

                <h1 class="text-white text-3xl">
                    Latest Updates & Thought Leadership
                </h1>

                <p class="text-lg/[22px] pt-2">
                    Stay informed about our upcoming projects, industry
                    insights, <br />
                    partnerships, and event participation
                </p>
            </div>

            <!-- Start Carousel -->
            <div class="relative sm:px-5 mt-14">
                <div
                    class="absolute top-1/2 start-8 -translate-y-1/2"
                    style="z-index: 5"
                >
                    <button
                        class="bg-brand-blue text-white bg-left-owl0 rounded-[50%] sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] border-2 border-white opacity-50"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>

                <div
                    class="absolute top-1/2 end-8 -translate-y-1/2"
                    style="z-index: 5"
                >
                    <button
                        class="bg-brand-blue text-white bg-right-owl0 rounded-[50%] sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] border-2 border-white opacity-50"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="owl-carousel owl-0">
                    <div class="items rounded-xl py-5">
                        <div
                            class="item-content-wrap relative border-5 border-white transition-all duration-200"
                        >
                            <img
                                src="@/assets/box-1.jpg"
                                class="w-full"
                                alt="News Image 5"
                            />
                            <!-- <div class="text-center absolute px-3 bottom-3 w-full">
                            <h1
                                class="item-title text-brand-blue px-2 bg-white text-lg/[20px] py-2 rounded-xl transition-all duration-200">
                                Esports & Community <br />Events
                            </h1>
                            </div> -->
                        </div>
                    </div>

                    <div class="items py-5">
                        <div
                            class="item-content-wrap relative border-5 border-white transition-all duration-200"
                        >
                            <img
                                src="@/assets/box-2.jpg"
                                class="w-full"
                                alt="News Image 5"
                            />
                            <!-- <div class="text-center absolute px-3 bottom-3 w-full">
                            <h1
                                class="item-title text-brand-blue px-2 bg-white text-lg/[20px] py-2 rounded-xl transition-all duration-200">
                                Tech & Innovation <br />Trends
                            </h1>
                            </div> -->
                        </div>
                    </div>

                    <div class="items py-5">
                        <div
                            class="item-content-wrap relative border-5 border-white transition-all duration-200"
                        >
                            <img
                                src="@/assets/box-3.jpg"
                                class="w-full"
                                alt="News Image 5"
                            />
                            <!-- <div class="text-center absolute px-3 bottom-3 w-full">
                            <h1
                                class="item-title text-brand-blue px-2 bg-white text-lg/[20px] py-2 rounded-xl transition-all duration-200">
                                Game Development <br />News
                            </h1>
                            </div> -->
                        </div>
                    </div>

                    <div class="items py-5">
                        <div
                            class="item-content-wrap relative border-5 border-white transition-all duration-200"
                        >
                            <img
                                src="@/assets/box-4.jpg"
                                class="w-full"
                                alt="News Image 5"
                            />
                            <!-- <div class="text-center absolute px-3 bottom-3 w-full">
                            <h1
                                class="item-title text-brand-blue px-2 bg-white text-lg/[20px] py-2 rounded-xl transition-all duration-200">
                                Game Development <br />News
                            </h1>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Carousel -->

            <!-- <div id="custom-owl-dots-0" class="flex justify-center gap-1 mt-12">
                <div class="owl-dot-custom w-[30px] h-[7px] bg-brand-blue cursor-pointer" data-slide-to="0"></div>
                <div class="owl-dot-custom w-[30px] h-[7px] bg-white cursor-pointer" data-slide-to="1"></div>
                <div class="owl-dot-custom w-[30px] h-[7px] bg-white cursor-pointer" data-slide-to="2"></div>
                <div class="owl-dot-custom w-[30px] h-[7px] bg-white cursor-pointer" data-slide-to="3"></div>
                <div class="owl-dot-custom w-[30px] h-[7px] bg-white cursor-pointer" data-slide-to="4"></div>
            </div> -->
        </div>
    </div>
    <!-- End New -->
</template>

<style scoped>
.owl-0 .item-content-wrap {
    border: 5px solid white;
    transition: all 0.4s ease;
    transform: scale(0.9);
}

.owl-0 .owl-item.center .item-content-wrap,
.owl-0 .item-content-wrap.active-center {
    border-color: #147cc3;
    transform: scale(1.05);
    z-index: 10;
    .item-title {
        background: #147cc3 !important;
        color: white !important;
        font-size: 20px !important;
    }
}
</style>
