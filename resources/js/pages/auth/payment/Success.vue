<script setup>
import { defineProps, onMounted } from "vue"; // Added onMounted
import { Head } from "@inertiajs/vue3";
import logoIcon from "@/assets/logo.png";

// Define the constant key to match the Registration page
const LOCAL_STORAGE_KEY = "teamRegistrationDraft";

const props = defineProps({
    sessionId: {
        type: String,
        required: true,
    },
    teamName: {
        type: String,
        required: true,
    },
});

// Clear the registration draft only when this page is reached (after payment)
onMounted(() => {
    try {
        if (localStorage.getItem(LOCAL_STORAGE_KEY)) {
            localStorage.removeItem(LOCAL_STORAGE_KEY);
            console.log("Team registration draft cleared.");
        }
    } catch (e) {
        console.error("Error clearing state from localStorage:", e);
    }
});
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" :href="logoIcon" />
        <title>BB 88 Advertising & Digital Solution Inc.</title>
    </Head>
    <div
        class="bg-[url('@/assets/bg.jpg')] bg-cover bg-center py-12 bg-no-repeat min-h-screen w-full grid place-items-center"
    >
        <div class="relative mx-auto w-full max-w-[1500px] sm:px-10 px-5">
            <div class="mx-auto w-full max-w-[1320px] pb-10">
                <div class="flex flex-col md:flex-row justify-between">
                    <div></div>

                    <div
                        class="flex gap-5 flex-col sm:flex-row items-center justify-between"
                    >
                        <p class="text-white text-lg">In Cooperation with:</p>
                        <img
                            src="@/assets/landing-bb88-logo.png"
                            class="sm:h-[50px] sm:w-auto h-auto w-full"
                            alt=""
                        />
                    </div>
                </div>

                <div class="text-[#DCDBE0] flex gap-5 mt-5 text-lg">
                    <a href="/">RETURN HOME</a>
                </div>
            </div>

            <div
                class="p-2 rounded-2xl bg-gradient-to-r from-brand-blue to-brand-pink mx-auto w-full max-w-[720px]"
            >
                <div
                    class="bg-[url('@/assets/success_box.jpg')] bg-cover bg-center sm:p-5 p-3 bg-no-repeat w-full h-fit rounded-xl"
                >
                    <h1
                        class="font-gaming text-white text-center text-4xl whitespace-nowrap pt-22 pb-2"
                    >
                        <i> {{ props.teamName.toUpperCase() }} </i>
                    </h1>

                    <h1
                        class="font-gaming text-white text-center sm:text-4xl text-2xl pt-22 pb-2"
                    >
                        <i> THANK YOU FOR YOUR SUCCESSFUL REGISTRATION </i>
                    </h1>

                    <p class="text-white text-center text-md">
                        ALWAYS CHECK YOUR EMAILS OR MESSAGES TO DOWNLOAD THE
                        <br />
                        OFFICIAL APP WHERE YOU CAN LOG IN YOUR ACCOUNT!
                    </p>
                </div>
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
