<script setup>
import { Head } from "@inertiajs/vue3";
import logoIcon from "@/assets/logo.png";
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" :href="logoIcon" />
    </Head>
    <div
        class="bg-[url('@/assets/bg.jpg')] bg-cover bg-center py-12 bg-no-repeat min-h-screen w-full grid place-items-center"
    >
        <div class="mx-auto w-full max-w-[1320px] px-4">
            <div class="text-[#DCDBE0] flex gap-5 text-lg">
                <a href="">MORE INFO</a>
            </div>
            <!-- <img
                src="@/assets/landing-cinco-logo.png"
                class="mx-auto sm:w-[400px] w-full pt-8 pb-5"
                alt=""
            /> -->

            <div
                class="flex gap-5 flex-col sm:flex-row items-center pt-8 justify-center"
            >
                <p class="text-white text-xl">In Cooperation with:</p>
                <img
                    src="@/assets/landing-bb88-logo.png"
                    class="sm:h-[60px] h-auto sm:w-auto w-full"
                    alt=""
                />
            </div>

            <h1
                class="font-gaming text-center text-white md:text-7xl sm:text-5xl text-4xl pt-12"
            >
                <i>ENTER THE BATTLEZONE</i>
            </h1>

            <img
                src="@/assets/join.jpg"
                class="md:rounded-2xl rounded-xl mt-7"
                alt=""
            />

            <div class="flex justify-center sm:pt-14 pt-10 w-full">
                <a href="/register">
                    <img
                        src="@/assets/reg-button1.png"
                        class="w-[500px] transition-transform duration-300 hover:scale-105"
                        alt=""
                    />
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
@font-face {
    font-family: "GamingSporty";
    src: url("../../../fonts/GamingSporty.ttf") format("truetype");
    font-weight: normal;
    font-style: normal;
}

.font-gaming {
    font-family: "GamingSporty", sans-serif;
}
</style>
