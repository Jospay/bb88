<template>
    <!-- Start About -->
    <div
        id="aboutus"
        class="bg-[url('@/assets/nbg123.png')] bg-cover bg-black bg-no-repeat pt-10 scroll-mt-17"
    >
        <!-- Start Deskop View Only -->
        <div
            class="bg-gradient-to-r from-transparent via-brand-blue/90 to-brand-blue relative lg:block hidden"
        >
            <div
                class="absolute bottom-0 h-[50px] w-[70%] end-0 bg-black"
                style="clip-path: polygon(40px 0%, 100% 0%, 100% 100%, 0% 100%)"
            ></div>

            <div
                class="absolute bottom-0 h-[50px] w-[8px] end-[69.9%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>
            <div
                class="absolute bottom-0 h-[50px] w-[8px] end-[71.5%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>
            <div
                class="absolute bottom-0 h-[50px] w-[8px] end-[73.1%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>
            <div
                class="absolute bottom-0 h-[50px] w-[8px] end-[74.8%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>

            <div class="w-full max-w-[1320px] mx-auto">
                <h1 class="text-white font-bold text-3xl py-4 xl:ps-3 ps-8">
                    ABOUT US
                </h1>
            </div>
        </div>
        <!-- End Deskop View Only -->

        <!-- Start Mobile View Only -->
        <div
            class="bg-gradient-to-r from-transparent via-brand-blue/90 to-brand-blue relative lg:hidden"
        >
            <div class="text-center">
                <h1 class="text-white font-bold text-3xl py-4">ABOUT US</h1>
            </div>
        </div>
        <!-- End Mobile View Only -->

        <!-- Start full Width -->
        <div class="w-full max-w-[1320px] mx-auto pt-12 px-5 lg:pb-0 pb-10">
            <!-- Start Who we Are -->
            <div class="grid grid-cols-12 items-center">
                <!-- left side -->
                <div class="lg:block hidden col-span-5 text-center">
                    <img
                        src="@/assets/logo about.png"
                        class="mx-auto max-w-[290px]"
                        alt=""
                    />
                </div>

                <!-- right side -->
                <div class="lg:col-span-7 col-span-12">
                    <div
                        class="bg-gradient-to-r from-transparent via-brand-lightgreen/90 to-brand-lightgreen w-fit"
                        style="
                            clip-path: polygon(
                                0 0%,
                                calc(100% - 40px) 0%,
                                75% 100%,
                                0 100%
                            );
                        "
                    >
                        <h1
                            class="text-white font-bold sm:text-4xl text-3xl sm:ps-5 px-2 sm:pe-[90px] pe-[80px] py-2"
                        >
                            Who we Are?
                        </h1>
                    </div>

                    <p class="text-white pt-5 sm:text-lg/[28px] text-md/[25px]">
                        <strong>Cinco Digital Inc.</strong> is a dynamic digital
                        entertainment and technology company specializing in
                        gaming innovation for the global market. We bring
                        together talent, technology, and creativity to deliver
                        immersive, world-class gaming experiences that resonate
                        with Filipino and international audiences.
                    </p>

                    <p class="text-white pt-7 sm:text-lg/[28px] text-md/[25px]">
                        As a one-stop hub for gaming innovation, we offer
                        end-to-end solutions, from concept creation and design
                        to live operations and post-launch support. Cinco
                        Digital isn’t just a developer; we are builders of
                        digital ecosystems that connect creators, brands, and
                        players through the universal language of play.
                    </p>
                </div>
            </div>
            <!-- Start Who we Are -->

            <!-- Start core Values -->
            <div
                class="grid grid-cols-12 items-center bg-gradient-to-t from-transparent via-brand-red/50 to-brand-red/50 px-5 py-8 rounded-xl mt-8"
            >
                <div
                    class="lg:col-span-5 col-span-12 lg:mb-0 mb-4 text-center text-white sm:text-4xl text-3xl font-bold"
                >
                    Core Values
                </div>

                <div
                    class="lg:col-span-7 col-span-12 text-white sm:text-lg text-md"
                >
                    <p>
                        <strong>Creativity</strong> – Inspiring bold, original
                        ideas.
                    </p>
                    <p>
                        <strong>Integrity</strong> – Building trust through
                        fairness and honesty.
                    </p>
                    <p>
                        <strong>Innovation </strong> – Embracing technology to
                        redefine gaming.
                    </p>
                    <p>
                        <strong>Collaboration</strong> – Empowering teams and
                        partners to create together.
                    </p>
                    <p>
                        <strong>Excellence</strong> – Committed to precision,
                        quality, and growth.
                    </p>
                    <p>
                        <strong>Community</strong> – Supporting Filipino talent
                        and the global gaming ecosystem.
                    </p>
                </div>
            </div>
            <!-- End core Values -->

            <!-- Start Strategic Goals -->
            <div
                class="grid grid-cols-12 items-center bg-gradient-to-t from-transparent via-brand-yellow/50 to-brand-yellow/50 px-5 py-8 rounded-xl mt-8"
            >
                <div
                    class="lg:col-span-5 col-span-12 lg:mb-0 mb-4 text-center text-white sm:text-4xl text-3xl font-bold"
                >
                    Strategic Goals
                </div>

                <div class="lg:col-span-7 col-span-12 text-white text-lg">
                    <p>
                        – Establish the Philippines as a global gaming
                        innovation hub.
                    </p>
                    <p>
                        – Create internationally recognized Filipino-made games.
                    </p>
                    <p>– Partner with global studios and tech leaders.</p>
                    <p>– Foster education in creative technologies.</p>
                    <p>– Support sustainable growth in the digital industry.</p>
                </div>
            </div>
            <!-- EndStrategic Goals -->
        </div>
        <!-- Endfull Width -->

        <!-- Start border bottom -->
        <div
            class="bg-gradient-to-r from-transparent via-brand-blue/90 to-brand-blue relative mt-[45px] lg:block hidden"
        >
            <div
                class="absolute top-0 h-[50px] w-[70%] end-0 bg-black"
                style="clip-path: polygon(40px 0%, 100% 0%, 100% 100%, 0% 100%)"
            ></div>

            <div
                class="absolute top-0 h-[50px] w-[8px] end-[69.9%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>
            <div
                class="absolute top-0 h-[50px] w-[8px] end-[71.5%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>
            <div
                class="absolute top-0 h-[50px] w-[8px] end-[73.1%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>
            <div
                class="absolute top-0 h-[50px] w-[8px] end-[74.8%] bg-black"
                style="transform: skew(-40deg); transform-origin: center"
            ></div>

            <div class="w-full max-w-[1320px] mx-auto h-[70px]"></div>
        </div>
        <!-- End border bottom -->
    </div>
    <!-- End About -->
</template>
