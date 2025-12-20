<script setup>
import { onMounted } from "vue";

onMounted(() => {
    if (typeof $ === "undefined") {
        console.error("jQuery not loaded");
        return;
    }

    // Initialize Owl Carousel (owl-1)
    if ($(".owl-1").length > 0) {
        var owl1 = $(".owl-1").owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 20,
            smartSpeed: 1000,
            autoplay: false,
            autoplayTimeout: 4000,
            nav: false,
            dots: false,
            pauseOnHover: false,
            responsive: {
                423: {
                    margin: 20,
                    items: 2,
                },
                900: {
                    margin: 20,
                    stagePadding: 0,
                    items: 3,
                },
                1200: {
                    margin: 20,
                    stagePadding: 0,
                    items: 4,
                },
            },
        });

        // Navigation Buttons
        $(".bg-left-owl1").click(() => {
            owl1.trigger("prev.owl.carousel", [1000]);
        });

        $(".bg-right-owl1").click(() => {
            owl1.trigger("next.owl.carousel", [1000]);
        });
    }
});
</script>

<template>
    <!-- Start Career -->
    <div
        id="careers"
        class="text-center bg-[url('@/assets/car1.jpg')] bg-center bg-cover bg-no-repeat text-white py-8 px-3 scroll-mt-23"
    >
        <h1 class="text-white font-bold text-4xl text-center">CAREERS</h1>
        <h1 class="text-white font-bold text-2xl pt-5">
            Join Our Mission to Play Beyond Imagination
        </h1>

        <p class="text-lg/[22px] pt-2">
            We’re always looking for passionate creators, developers, and
            innovators to join our growing team.
        </p>
    </div>
    <!-- End Career -->

    <!-- Start Apply Career -->
    <div
        class="text-center bg-[url('@/assets/bg6.jpg')] bg-center bg-cover bg-no-repeat py-8"
    >
        <div class="w-full max-w-[1320px] px-5 mx-auto">
            <div class="relative">
                <!-- slide button start -->
                <div
                    class="absolute top-1/2 start-3 -translate-y-1/2"
                    style="z-index: 5"
                >
                    <button
                        class="bg-brand-blue text-white bg-left-owl1 rounded-[50%] sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] border-2 border-white opacity-50"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>

                <div
                    class="absolute top-1/2 end-3 -translate-y-1/2"
                    style="z-index: 5"
                >
                    <button
                        class="bg-brand-blue text-white bg-right-owl1 rounded-[50%] sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] border-2 border-white opacity-50"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <!-- slide button start -->

                <!-- Start carouse -->
                <div class="owl-carousel owl-1">
                    <div class="items">
                        <div class="border-5 rounded-2xl border-white relative">
                            <img
                                src="@/assets/c1.jpg"
                                class="rounded-xl w-full"
                                alt=""
                            />
                            <h1
                                class="text-white font-bold text-2xl/[30px] absolute bottom-5 start-0 end-0 mx-auto"
                            >
                                Game Developer <br />
                                (Unity/unreal)
                            </h1>
                        </div>
                    </div>

                    <div class="items">
                        <div class="border-5 rounded-2xl border-white relative">
                            <img
                                src="@/assets/c2.jpg"
                                class="rounded-xl w-full"
                                alt=""
                            />
                            <h1
                                class="text-white font-bold text-2xl/[30px] absolute bottom-5 start-0 end-0 mx-auto"
                            >
                                2D/3D Artists <br />
                                & animators
                            </h1>
                        </div>
                    </div>

                    <div class="items">
                        <div class="border-5 rounded-2xl border-white relative">
                            <img
                                src="@/assets/c3.jpg"
                                class="rounded-xl w-full"
                                alt=""
                            />
                            <h1
                                class="text-white font-bold text-2xl/[30px] absolute bottom-5 start-0 end-0 mx-auto"
                            >
                                Game Design <br />
                                & Writters
                            </h1>
                        </div>
                    </div>

                    <div class="items">
                        <div class="border-5 rounded-2xl border-white relative">
                            <img
                                src="@/assets/c4.jpg"
                                class="rounded-xl w-full"
                                alt=""
                            />
                            <h1
                                class="text-white font-bold text-2xl/[30px] absolute bottom-5 start-0 end-0 mx-auto"
                            >
                                Marketing & <br />
                                Community Specialists
                            </h1>
                        </div>
                    </div>

                    <div class="items">
                        <div class="border-5 rounded-2xl border-white relative">
                            <img
                                src="@/assets/c5.jpg"
                                class="rounded-xl w-full"
                                alt=""
                            />
                            <h1
                                class="text-white font-bold text-2xl/[30px] absolute bottom-5 start-0 end-0 mx-auto"
                            >
                                AR/VR <br />
                                Engineers
                            </h1>
                        </div>
                    </div>

                    <div class="items">
                        <div class="border-5 rounded-2xl border-white relative">
                            <img
                                src="@/assets/c6.jpg"
                                class="rounded-xl w-full"
                                alt=""
                            />
                            <h1
                                class="text-white font-bold text-2xl/[30px] absolute bottom-5 start-0 end-0 mx-auto"
                            >
                                Project <br />
                                Managers
                            </h1>
                        </div>
                    </div>
                </div>
                <!-- End carouse -->
            </div>

            <!-- Start Button -->
            <div class="text-center pt-9">
                <button
                    class="bg-brand-green px-20 text-white font-bold text-2xl rounded-xl py-2"
                >
                    Apply Now
                </button>
            </div>
            <!-- End Button -->
        </div>
    </div>
    <!-- Start Apply Career -->

    <!-- Start Resume -->
    <div class="bg-brand-blue py-2 text-white text-center text-lg">
        <p>or Send Resume to careers@cincodigital.ph</p>
    </div>
    <!-- End Resume -->
</template>
