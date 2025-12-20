<script setup>
import { onMounted } from "vue";
import Navbar from "@/components/landing/Navbar.vue";

onMounted(() => {
    // Check jQuery exists
    if (typeof $ === "undefined") {
        console.error("jQuery not loaded");
        return;
    }

    // Owl Carousel initialization
    if ($(".owl-3").length > 0) {
        const owl3 = $(".owl-3").owlCarousel({
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
                423: { items: 2, margin: 20 },
                900: { items: 3, margin: 20, stagePadding: 0 },
                1200: { items: 5, margin: 20, stagePadding: 0 },
            },
        });

        // Navigation buttons
        $(".bg-left-half-1").click(() => {
            owl3.trigger("prev.owl.carousel", [1000]);
        });

        $(".bg-right-half-1").click(() => {
            owl3.trigger("next.owl.carousel", [1000]);
        });
    }
});
</script>

<template>
    <!-- Start Banner -->
    <div
        id="home"
        class="bg-[url('@/assets/bg1.jpg')] bg-no-repeat bg-cover bg-fixed min-h-screen flex items-center justify-center xl:pt-[70px] pt-[50px] xl:pb-[70px] pb-[50px]"
    >
        <!-- Start Navbar -->
        <Navbar />
        <!-- End Navbar -->

        <div class="mt-18 px-4 w-full max-w-[1320px]">
            <h1
                class="xl:text-7xl/[85px] lg:text-6xl/[65px] md:text-5xl/[60px] sm:text-4xl/[50px] text-3xl/[45px] font-bold text-white text-center sm:block hidden"
            >
                Empowering the Future of Play <br />
                through Innovation and Technology
            </h1>

            <h1
                class="xl:text-7xl/[85px] lg:text-6xl/[65px] md:text-5xl/[60px] sm:text-4xl/[50px] text-3xl/[45px] font-bold text-white text-center sm:hidden block"
            >
                Empowering the Future of Play <br />
                through Innovation and Technology
            </h1>

            <p class="text-center text-white pt-6 md:block hidden">
                <strong>Cinco Digital Inc.</strong> is a Filipino-led digital
                entertainment and technology company redefining the future of
                <br />gaming through creativity, innovation, and next-generation
                technologies.
            </p>

            <p class="text-center text-white pt-6 md:hidden block">
                <strong>Cinco Digital Inc.</strong> is a Filipino-led digital
                entertainment and technology company redefining the future of
                gaming through creativity, innovation, and next-generation
                technologies.
            </p>

            <!-- Start Explore and join  -->
            <div class="md:flex grid justify-center md:gap-8 gap-4 pt-9">
                <div>
                    <a href="#services">
                        <button
                            class="bg-white text-brand-blue md:w-[350px] sm:w-[400px] w-[300px] py-2 font-bold text-2xl rounded-[10px]"
                        >
                            Explore Our Services
                        </button>
                    </a>
                </div>
                <div>
                    <a href="/join">
                        <button
                            class="text-white bg-brand-blue md:w-[300px] sm:w-[400px] w-[300px] py-2 font-bold text-2xl rounded-[10px]"
                        >
                            Join Our Team
                        </button>
                    </a>
                </div>
            </div>
            <!-- End Explore and join  -->

            <div class="mt-[60px] relative">
                <!-- slide button start -->
                <div
                    class="absolute top-1/2 start-2 -translate-y-1/2"
                    style="z-index: 5"
                >
                    <button
                        class="bg-brand-blue text-white bg-left-half-1 rounded-[50%] sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] opacity-50"
                        style="border: none"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>

                <div
                    class="absolute top-1/2 end-2 -translate-y-1/2"
                    style="z-index: 5"
                >
                    <button
                        class="bg-brand-blue text-white bg-right-half-1 rounded-[50%] sm:h-[40px] h-[35px] sm:w-[40px] w-[35px] opacity-50"
                        style="border: none"
                    >
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <!-- slide button start -->

                <!-- STart carousel -->
                <div class="owl-carousel owl-3">
                    <div
                        class="bg-white rounded-[15px] w-full py-[20px] h-[210px] grid place-items-center"
                    >
                        <img
                            src="@/assets/icon1.png"
                            class="h-[90px] w-auto object-contain"
                            alt=""
                        />
                        <p
                            class="text-brand-blue text-center font-bold text-xl/[25px]"
                        >
                            Full-Cycle Game <br />
                            Development
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-[15px] w-full py-[20px] h-[210px] grid place-items-center"
                    >
                        <img
                            src="@/assets/icon2.png"
                            class="h-[90px] w-auto object-contain"
                            alt=""
                        />
                        <p
                            class="text-brand-blue text-center font-bold text-xl/[25px]"
                        >
                            Global Game <br />
                            Publishing
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-[15px] w-full py-[20px] h-[210px] grid place-items-center"
                    >
                        <img
                            src="@/assets/icon3.png"
                            class="h-[90px] w-auto object-contain"
                            alt=""
                        />
                        <p
                            class="text-brand-blue text-center font-bold text-xl/[25px]"
                        >
                            AI, Blockchain & <br />
                            XR Integration
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-[15px] w-full py-[20px] h-[210px] grid place-items-center"
                    >
                        <img
                            src="@/assets/icon4.png"
                            class="h-[90px] w-auto object-contain"
                            alt=""
                        />
                        <p
                            class="text-brand-blue text-center font-bold text-xl/[25px]"
                        >
                            Global <br />
                            Partnerships
                        </p>
                    </div>

                    <div
                        class="bg-white rounded-[15px] w-full py-[20px] h-[210px] grid place-items-center"
                    >
                        <img
                            src="@/assets/icon5.png"
                            class="h-[90px] w-auto object-contain"
                            alt=""
                        />
                        <p
                            class="text-brand-blue text-center font-bold text-xl/[25px]"
                        >
                            Filipino <br />
                            Innocation
                        </p>
                    </div>
                </div>
                <!-- End carousel -->
            </div>
        </div>
    </div>
    <!-- End Banner -->
</template>
