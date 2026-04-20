<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import logoIcon from "@/assets/logo.png";

// Constants
const SHIRT_PRICE = 600;
const INITIAL_PLAYER_COUNT = 5;
const BASE_REGISTRATION_FEE = (INITIAL_PLAYER_COUNT + 1) * SHIRT_PRICE;

// Reactive State
const additionalShirtCount = ref(0);
const agreedToPrivacy = ref(false); // Link to checkbox
const players = ref(Array(5).fill({}));
const reservePlayer = ref({ size_shirt: "" });
const availShirts = computed(() => Array(additionalShirtCount.value).fill({}));
const availShirtDetails = ref([]);

// Logic
const totalPayment = computed(() => {
    return BASE_REGISTRATION_FEE + additionalShirtCount.value * SHIRT_PRICE;
});

const incrementShirt = () => {
    additionalShirtCount.value++;
    availShirtDetails.value.push({ size_shirt: "" });
};

const decrementShirt = () => {
    if (additionalShirtCount.value > 0) {
        additionalShirtCount.value--;
        availShirtDetails.value.pop();
    }
};

const registerTeam = () => {
    if (!agreedToPrivacy.value) {
        alert("Please agree to the Privacy Policy first.");
        return;
    }
    // Your registration logic here
    console.log("Processing Payment...");
};
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" :href="logoIcon" />
    </Head>
    <div
        class="bg-[url('@/assets/bg.jpg')] bg-cover bg-fixed bg-center py-12 bg-no-repeat min-h-screen w-full grid place-items-center"
    >
        <div class="mx-auto w-full max-w-[1320px] px-5">
            <div class="flex flex-col md:flex-row justify-end">
                <div
                    class="flex gap-5 flex-col sm:flex-row items-center justify-between"
                >
                    <p class="text-white text-lg">In Cooperation with:</p>
                    <img
                        src="@/assets/landing-bb88-logo.png"
                        class="sm:h-[50px] h-auto sm:w-auto w-full"
                        alt=""
                    />
                </div>
            </div>
            <div class="text-[#DCDBE0] flex gap-5 mt-7 sm:text-lg text-md">
                <a href="/register">BACK</a>
            </div>
        </div>

        <div class="mt-4 pb-3 relative mx-auto w-full max-w-[1500px] px-5">
            <div
                class="absolute inset-0 opacity-80"
                style="
                    background: linear-gradient(
                        to right,
                        rgba(20, 124, 195, 0),
                        #147cc3 25%,
                        #bf38a6 75%,
                        rgba(191, 56, 166, 0)
                    );
                "
            ></div>
            <div class="mx-auto w-full max-w-[1320px]">
                <h1
                    class="font-gaming text-center md:pt-0 pt-2 text-white md:text-5xl text-3xl relative z-10"
                >
                    <i>REGISTRATION STEP-BY-STEP GUIDE</i>
                </h1>
            </div>
        </div>

        <div
            class="mt-12 relative mx-auto w-full max-w-[1500px] pt-8 pb-8 sm:px-10"
        >
            <div
                class="opacity-80"
                style="
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(
                        to right,
                        rgba(20, 124, 195, 0),
                        #147cc3 25%,
                        #bf38a6 75%,
                        rgba(191, 56, 166, 0)
                    );
                    z-index: 0;
                "
            ></div>

            <form
                @submit.prevent="registerTeam"
                class="mx-auto w-full max-w-[1320px] px-5"
            >
                <div class="p-1 border-2 border-white z-10 relative">
                    <p class="text-center text-sm py-2 text-white">
                        Enter a unique team name. If the name is already taken,
                        you will need to choose a different one before
                        submitting.
                    </p>
                    <div class="sm:flex grid items-center gap-4 pb-1 relative">
                        <h1
                            class="font-gaming text-white mx-auto sm:text-3xl text-2xl"
                        >
                            <i>TEAM NAME:</i>
                        </h1>
                        <div class="flex-1">
                            <input
                                type="text"
                                value="Malakas"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 rounded-md outline-none ring-2 ring-[#147cc3] cursor-not-allowed"
                                disabled
                            />
                        </div>
                    </div>
                </div>

                <div class="relative z-10">
                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-center text-sm py-2 text-white">
                            Select your location in order: Region, Province,
                            City, and Barangay. Finally, enter your Postal Code.
                        </p>
                        <div class="py-5 text-center">
                            <h1
                                class="font-gaming text-white sm:text-3xl text-2xl"
                            >
                                <i>ADDRESS</i>
                            </h1>

                            <p class="text-[#d8d4d4] text-sm">
                                Please provide the complete address you want to
                                represent
                            </p>
                        </div>

                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
                        <div class="grid grid-cols-12 gap-4">
                            <div
                                class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">Country</label>
                                <div class="relative">
                                    <img
                                        src="@/assets/ph.jpg"
                                        class="absolute ps-3 pt-1 top-1/2 -translate-y-1/2 left-0"
                                        alt=""
                                    />
                                    <input
                                        type="text"
                                        value="Philippines"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 text-center mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">Region</label>
                                <select
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                >
                                    <option>Central Luzon</option>
                                </select>
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">Province</label>
                                <select
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                >
                                    <option>Pampanga</option>
                                </select>
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">City</label>
                                <select
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                >
                                    <option>City of Angeles</option>
                                </select>
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">Barangay</label>
                                <select
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                >
                                    <option>Sto. Domingo</option>
                                </select>
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">Postal Code</label>
                                <input
                                    type="text"
                                    value="2009"
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                />
                            </div>
                        </div>
                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-center text-sm py-2 text-white">
                            Enter details for each player: Full Name, IGN (ML
                            username), Email, and Mobile Number. Choose a shirt
                            size (L, XL, or XXL). Note: Each player must have a
                            unique in-game name, email and mobile number;
                            duplicates are not allowed.
                        </p>

                        <div class="py-5 text-center">
                            <h1
                                class="font-gaming text-white sm:text-3xl text-2xl"
                            >
                                <i>TEAM MEMBER'S</i>
                            </h1>

                            <p class="text-[#d8d4d4] text-sm">
                                Make sure every member's details are correct, as
                                they cannot be edited once submitted.
                            </p>
                        </div>

                        <template v-for="(p, index) in players" :key="index">
                            <div
                                class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                            ></div>
                            <div class="grid grid-cols-12 gap-4">
                                <div
                                    class="xl:col-span-2 md:col-span-4 col-span-12"
                                >
                                    <label class="text-white"
                                        ><span class="font-bold"
                                            >(Player {{ index + 1 }})</span
                                        >
                                        Full Name</label
                                    >
                                    <input
                                        type="text"
                                        value="Juan Dela Cruz"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-2 md:col-span-4 col-span-12"
                                >
                                    <label class="text-white"
                                        >(IGN) In-Game Name</label
                                    >
                                    <input
                                        type="text"
                                        value="Dogie"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-3 md:col-span-4 col-span-12"
                                >
                                    <label class="text-white">E-mail</label>
                                    <input
                                        type="email"
                                        value="juandelacruz@cca.eud.ph"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white"
                                        >Mobile Number</label
                                    >
                                    <input
                                        type="number"
                                        value="9123123123"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-2 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white">Shirt Size</label>
                                    <select
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    >
                                        <option>XL</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-center text-sm py-2 text-white">
                            Fill out the information for your reserve player.
                            They will only play if a main team member is injured
                            or unavailable. A unique in-game name, email and
                            contact number are also required.
                        </p>
                        <div class="py-5 text-center">
                            <h1
                                class="font-gaming text-white sm:text-3xl text-2xl"
                            >
                                <i>RESERVE PLAYER</i>
                            </h1>

                            <p class="text-[#d8d4d4] text-sm">
                                Make sure the reserve player details are
                                correct, as they cannot be edited once
                                submitted.
                            </p>
                        </div>
                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
                        <div class="grid grid-cols-12 gap-4">
                            <div
                                class="xl:col-span-2 md:col-span-4 col-span-12"
                            >
                                <label class="text-white"
                                    ><span class="font-bold">(Reserve)</span>
                                    Full Name</label
                                >
                                <input
                                    type="text"
                                    value="Pedro Dela Santos"
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                />
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-4 col-span-12"
                            >
                                <label class="text-white"
                                    >(IGN) In-Game Name</label
                                >
                                <input
                                    type="text"
                                    value="malakas_pedro"
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                />
                            </div>
                            <div
                                class="xl:col-span-3 md:col-span-4 col-span-12"
                            >
                                <label class="text-white">E-mail</label>
                                <input
                                    value="pedrodelasantos@cca.edu.ph"
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                />
                            </div>
                            <div
                                class="xl:col-span-3 md:col-span-6 col-span-12"
                            >
                                <label class="text-white">Mobile Number</label>
                                <input
                                    type="number"
                                    value="9111111111"
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                />
                            </div>
                            <div
                                class="xl:col-span-2 md:col-span-6 col-span-12"
                            >
                                <label class="text-white">Shirt Size</label>
                                <select
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                    disabled
                                >
                                    <option>XXL</option>
                                </select>
                            </div>
                        </div>
                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
                    </div>

                    <template v-if="additionalShirtCount > 0">
                        <div class="py-5 text-center">
                            <h1
                                class="font-gaming text-white sm:text-3xl text-2xl"
                            >
                                <i>AVAILING SHIRT DETAILS</i>
                            </h1>

                            <p class="text-[#d8d4d4] text-sm">
                                Make sure the shirt details are correct, as they
                                cannot be edited once submitted.
                            </p>
                        </div>

                        <template
                            v-for="(s, index) in availShirts"
                            :key="index"
                        >
                            <div
                                class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                            ></div>
                            <div class="grid grid-cols-12 gap-4">
                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white"
                                        ><span class="font-bold"
                                            >(Shirt {{ index + 1 }})</span
                                        >
                                        Name</label
                                    >
                                    <input
                                        type="text"
                                        value="Maria Santos"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white">E-mail</label>
                                    <input
                                        type="email"
                                        value="mariasantos@gmail.com"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white">Mobile</label>
                                    <input
                                        type="number"
                                        value="9222222222"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    />
                                </div>
                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white">Size</label>
                                    <select
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] cursor-not-allowed"
                                        disabled
                                    >
                                        <option>XL</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
                    </template>

                    <!-- =============== PRIVACY CHECKBOX =============== -->
                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm py-2 text-white">
                            Click the Privacy Policy link to read it. Once you
                            have reviewed the information, click 'Agree' to
                            automatically check the box and proceed.
                        </p>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    class="mt-1 h-4 w-4 text-brand focus:ring-brand border-gray-300 rounded"
                                    checked
                                />

                                <label for="agree" class="text-sm text-white">
                                    I agree to the
                                    <span
                                        class="text-brand-green font-bold hover:underline"
                                    >
                                        Privacy Policy
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div
                        class="xl:flex grid xl:justify-between justify-center mt-4 relative z-10"
                    >
                        <div>
                            <h1
                                class="font-gaming text-white sm:text-3xl text-2xl py-5"
                            >
                                <i>PAYMENT:</i>
                            </h1>
                        </div>
                        <div class="p-2 border-2 border-white mt-4">
                            <p class="text-sm text-center py-2 mb-1 text-white">
                                The registration fee covers 6 members at ₱600
                                each. To buy extra shirts, click the '+' button
                                <br />
                                (₱600 per shirt). Ensure all extra shirt owners
                                have unique contact details. Click 'Submit'
                                <br />
                                once your total is correct.
                            </p>
                            <div class="grid grid-cols-6 gap-4">
                                <div class="sm:col-span-2 col-span-6">
                                    <label class="text-white text-[14px]"
                                        >Add on Shirt:</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="flex gap-2 absolute pe-2 pt-1 top-1/2 -translate-y-1/2 right-0"
                                        >
                                            <button
                                                type="button"
                                                @click="decrementShirt"
                                                :disabled="
                                                    additionalShirtCount === 0
                                                "
                                                class="bg-[#147cc3] text-white px-3 py-0 font-bold text-2xl disabled:opacity-40"
                                            >
                                                -
                                            </button>
                                            <button
                                                type="button"
                                                @click="incrementShirt"
                                                class="bg-[#147cc3] text-white px-2 py-0 font-bold text-2xl"
                                            >
                                                +
                                            </button>
                                        </div>
                                        <input
                                            type="text"
                                            :value="additionalShirtCount"
                                            readonly
                                            class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#147cc3]"
                                        />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 col-span-6">
                                    <label class="text-white text-[14px]"
                                        >Total</label
                                    >
                                    <input
                                        type="text"
                                        :value="`₱ ${totalPayment.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-green-500"
                                        readonly
                                    />
                                </div>

                                <div class="sm:col-span-2 col-span-6">
                                    <button
                                        type="submit"
                                        :disabled="!agreedToPrivacy"
                                        class="sm:py-[3px] py-[5px] sm:mt-[23px] mt-4 w-full text-2xl bg-green-600 text-white rounded-md border-2 border-white disabled:opacity-30 disabled:cursor-not-allowed transition-opacity"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                            <p
                                class="text-[#d8d4d4] xl:text-start text-center text-sm pt-2"
                            >
                                Note: Every add-on shirt is worth ₱ 600.00
                                (Purchasing an add-on shirt does not grant
                                eligibility to play).
                                <span class="hidden sm:inline">
                                    <br />
                                </span>
                                The 5 main players automatically receive shirts
                                upon registration, totaling
                                {{
                                    BASE_REGISTRATION_FEE.toLocaleString(
                                        "en-PH",
                                        {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2,
                                        },
                                    )
                                }}.

                                <span class="block mt-1 italic text-[#f3f3f3]">
                                    * Reserve players are also included in the
                                    shirt allocation and will receive one.
                                    <br />
                                    They will only become eligible to play if
                                    they replace a main player.
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            After clicking the submit button, the system will
                            start loading. Please take note:
                            <strong
                                >do not close the window or refresh the
                                page</strong
                            >
                            because your email and phone number are still being
                            processed for sending the email and SMS.
                        </p>
                        <img
                            src="@/assets/loading.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Registration Loading"
                        />
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p
                            class="text-sm text-center py-2 mb-1 text-white leading-relaxed"
                        >
                            This page confirms that your registration has been
                            successfully completed. To continue, log in using
                            your registered team email and follow the steps
                            below to proceed with your payment.
                        </p>

                        <img
                            src="@/assets/teams.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Registration Success"
                        />
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            To generate your password, please follow the steps
                            below:
                        </p>

                        <div class="grid grid-cols-12 gap-5">
                            <div class="col-span-4 border p-2">
                                <p
                                    class="text-sm text-center py-2 mb-1 text-white"
                                >
                                    Click the "Forgot Password"
                                </p>
                                <img
                                    src="@/assets/playerLogin.JPG"
                                    class="img-fluid rounded-2xl"
                                    alt="Forgot Password Step"
                                />
                            </div>

                            <div class="col-span-4 border p-2">
                                <p
                                    class="text-sm text-center py-2 mb-1 text-white"
                                >
                                    Enter your registered team email and click
                                    "Send Reset Link" button.
                                </p>
                                <img
                                    src="@/assets/reset.JPG"
                                    class="img-fluid rounded-2xl"
                                    alt="Reset Password Step"
                                />
                            </div>

                            <div class="col-span-4 border p-2">
                                <p
                                    class="text-sm text-center py-2 mb-1 text-white"
                                >
                                    Once submitted, check your email for the
                                    password reset notification.
                                </p>
                                <img
                                    src="@/assets/successRequest.JPG"
                                    class="img-fluid rounded-2xl"
                                    alt="Email Sent Confirmation"
                                />
                            </div>

                            <div class="col-span-4 border p-2">
                                <p
                                    class="text-sm text-center py-2 mb-1 text-white"
                                >
                                    After receiving the email, click the "Verify
                                    and Create a New Password" button.
                                </p>
                                <img
                                    src="@/assets/email.JPG"
                                    class="img-fluid rounded-2xl"
                                    alt="Email Verification Step"
                                />
                            </div>

                            <div class="col-span-4 border p-2">
                                <p
                                    class="text-sm text-center py-2 mb-1 text-white"
                                >
                                    Create a strong password and confirm it to
                                    complete the process.
                                </p>
                                <img
                                    src="@/assets/createPass.JPG"
                                    class="img-fluid rounded-2xl"
                                    alt="Create Password Step"
                                />
                            </div>

                            <div class="col-span-4 border p-2">
                                <p
                                    class="text-sm text-center py-2 mb-1 text-white"
                                >
                                    Once your password is successfully updated,
                                    click "Continue to Dashboard" to proceed
                                    with your payment.
                                </p>
                                <img
                                    src="@/assets/passwordUpdated.JPG"
                                    class="img-fluid rounded-2xl"
                                    alt="Password Updated Success"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            This is your dashboard, where you can view your team
                            list and proceed with your registration payment.
                            Click the "Pay Now" button to continue.
                        </p>

                        <img
                            src="@/assets/dashTeam.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Dashboard Team List"
                        />
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            This is the payment page, where you can choose your
                            preferred payment method to complete your
                            registration.
                        </p>

                        <img
                            src="@/assets/paymongo_choose.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Payment Method Selection"
                        />
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            Once your payment is successful, click the "Return
                            to Merchant" button and wait for confirmation. A
                            verification email will be sent to confirm that your
                            payment has been received.
                        </p>

                        <img
                            src="@/assets/paymentReceived.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Payment Confirmation Success"
                        />
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            This confirms that your payment was successful.
                            Please check your
                            <strong>Email</strong> to verify that you have
                            received your official registration notice.
                        </p>

                        <img
                            src="@/assets/paymentSuccess.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Payment Success Confirmation"
                        />
                    </div>

                    <div class="p-2 border-2 border-white mt-4">
                        <p class="text-sm text-center py-2 mb-1 text-white">
                            This is a sample of the email you will receive after
                            successful registration.
                        </p>

                        <img
                            src="@/assets/congrats.JPG"
                            class="img-fluid rounded-2xl"
                            alt="Email Sample Congratulations"
                        />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@font-face {
    font-family: "GamingSporty";
    src: url("../../../fonts/GamingSporty.ttf") format("truetype");
}
.font-gaming {
    font-family: "GamingSporty", sans-serif;
}
</style>
