<script setup>
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { useAddress } from "@/composables/useAddress";
import PrivacyPolicy from "@/components/privacy-policy/Privacy-policy.vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import logoIcon from "@/assets/logo.png";

const SHIRT_PRICE = 500;
const INITIAL_PLAYER_COUNT = 5;
const BASE_REGISTRATION_FEE = INITIAL_PLAYER_COUNT * SHIRT_PRICE;
const LOCAL_STORAGE_KEY = "teamRegistrationDraft";

const {
    regions,
    provinces,
    cities,
    barangays,
    selectedRegion,
    selectedProvince,
    selectedCity,
    selectedBarangay,
} = useAddress();

const teamName = ref("");
const postalCode = ref("");
const players = ref([]);
// Added Reserve Player State
const reservePlayer = ref({
    fullName: "",
    username: "",
    email: "",
    mobileNumber: "",
    accountType: "Reserve",
});
const additionalShirtCount = ref(0);
const availShirtDetails = ref([]);

const isSubmitting = ref(false);
const isProcessingPayment = ref(false); // NEW: Added this state for the button
const submitMessage = ref("");
const submitError = ref(null);
const validationErrors = ref({});

const isSuccessfullySubmitted = ref(false);

const availShirts = computed(() =>
    Array.from({ length: additionalShirtCount.value }, (_, index) => {
        if (!availShirtDetails.value[index]) {
            availShirtDetails.value[index] = {
                fullName: "",
                username: "N/A",
                email: "",
                mobileNumber: "",
                accountType: "Shirt",
            };
        }
        return availShirtDetails.value[index];
    })
);

const totalPayment = computed(
    () => BASE_REGISTRATION_FEE + additionalShirtCount.value * SHIRT_PRICE
);

const loadFormState = () => {
    const storedData = localStorage.getItem(LOCAL_STORAGE_KEY);
    if (storedData) {
        const data = JSON.parse(storedData);
        teamName.value = data.teamName || "";
        postalCode.value = data.postalCode || "";
        players.value =
            data.players ||
            Array.from({ length: INITIAL_PLAYER_COUNT }, () => ({
                fullName: "",
                username: "",
                email: "",
                mobileNumber: "",
                accountType: "Player",
            }));
        // Load Reserve Player
        if (data.reservePlayer) reservePlayer.value = data.reservePlayer;

        additionalShirtCount.value = data.additionalShirtCount || 0;
        availShirtDetails.value = data.availShirtDetails || [];
    } else {
        resetPlayers();
    }
};

const resetPlayers = () => {
    players.value = Array.from({ length: INITIAL_PLAYER_COUNT }, () => ({
        fullName: "",
        username: "",
        email: "",
        mobileNumber: "",
        accountType: "Player",
    }));
    reservePlayer.value = {
        fullName: "",
        username: "",
        email: "",
        mobileNumber: "",
        accountType: "Reserve",
    };
};

const saveFormState = () => {
    if (isSuccessfullySubmitted.value) return;

    localStorage.setItem(
        LOCAL_STORAGE_KEY,
        JSON.stringify({
            teamName: teamName.value,
            postalCode: postalCode.value,
            players: players.value,
            reservePlayer: reservePlayer.value, // Save Reserve
            additionalShirtCount: additionalShirtCount.value,
            availShirtDetails: availShirtDetails.value,
        })
    );
};

onMounted(loadFormState);

watch(
    [
        teamName,
        postalCode,
        players,
        reservePlayer,
        additionalShirtCount,
        availShirtDetails,
    ],
    saveFormState,
    { deep: true }
);

// Combined all users including reserve for duplicate checking
const allUsers = computed(() => {
    const list = [...players.value];
    if (reservePlayer.value.fullName || reservePlayer.value.email) {
        list.push(reservePlayer.value);
    }
    return [...list, ...availShirtDetails.value];
});

const duplicateEmails = computed(() => {
    const emails = allUsers.value.map((u) => u.email?.trim().toLowerCase());
    return emails.filter((email, i) => email && emails.indexOf(email) !== i);
});

const duplicateMobiles = computed(() => {
    const mobiles = allUsers.value.map((u) =>
        String(u.mobileNumber || "").trim()
    );
    return mobiles.filter((num, i) => num && mobiles.indexOf(num) !== i);
});

const duplicateUsernames = computed(() => {
    const usernames = allUsers.value
        .map((u) => u.username?.trim().toLowerCase())
        .filter((name) => name !== "n/a" && name !== "");
    return usernames.filter((name, i) => usernames.indexOf(name) !== i);
});

const handleInput = (fieldName) => {
    // 1. Remove the red line/specific error message
    if (validationErrors.value && validationErrors.value[fieldName]) {
        delete validationErrors.value[fieldName];
    }

    // 2. Clear the bottom "Validation failed" message if no more backend errors exist
    if (Object.keys(validationErrors.value).length === 0) {
        submitError.value = null;
    }
};

const getError = (fieldName, value = null, type = null) => {
    if (fieldName && validationErrors.value[fieldName]) {
        return validationErrors.value[fieldName][0];
    }

    if (type === "email" && value) {
        if (duplicateEmails.value.includes(value.trim().toLowerCase()))
            return "Duplicate email detected";
    }

    if (type === "mobile" && value) {
        const valStr = String(value).trim();
        if (duplicateMobiles.value.includes(valStr))
            return "Duplicate mobile number detected";
        if (valStr.length > 0 && valStr.length !== 10)
            return "Mobile number must be 10 digits";
    }

    if (type === "username" && value && value.toLowerCase() !== "n/a") {
        if (duplicateUsernames.value.includes(value.trim().toLowerCase()))
            return "Duplicate IGN/Username detected";
    }

    return null;
};

const incrementShirt = () => additionalShirtCount.value++;
const decrementShirt = () => {
    if (additionalShirtCount.value > 0) {
        additionalShirtCount.value--;
        availShirtDetails.value.pop();
    }
};

const registerTeam = async () => {
    // 1. Reset everything before starting
    submitError.value = null;
    validationErrors.value = {};

    // 2. Check Local Validation (Duplicates)
    const hasLocalErrors = allUsers.value.some((u) => {
        return (
            getError(null, u.email, "email") ||
            getError(null, u.mobileNumber, "mobile") ||
            getError(null, u.username, "username")
        );
    });

    if (hasLocalErrors) {
        submitError.value =
            "Please fix duplicate or formatting errors before submitting.";

        await nextTick();
        const firstError = document.querySelector(
            ".ring-red-500, .text-red-500"
        );
        if (firstError) {
            firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return;
    }

    // 3. Check Captain Email
    const captainEmail = players.value[0]?.email;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!captainEmail || !emailRegex.test(captainEmail)) {
        submitError.value =
            "The Captain (Player 1) must provide a valid email address for payment.";
        return;
    }

    isSubmitting.value = true;

    // 4. Prepare Payload
    const finalDetails = [...players.value];
    if (reservePlayer.value.fullName) {
        finalDetails.push(reservePlayer.value);
    }

    const allDetailUsers = [...finalDetails, ...availShirtDetails.value].map(
        (u) => ({
            fullName: u.fullName,
            username: u.accountType === "Shirt" ? null : u.username || "",
            email: u.email,
            mobileNumber: String(u.mobileNumber),
            accountType: u.accountType,
        })
    );

    const payload = {
        team: {
            team_name: teamName.value,
            total_payment: totalPayment.value,
            additional_shirt_count: additionalShirtCount.value,
            region: selectedRegion.value,
            province: selectedProvince.value,
            city: selectedCity.value,
            barangay: selectedBarangay.value,
            postal_code: postalCode.value,
            agree: agreeChecked.value,
        },
        details: allDetailUsers,
    };

    try {
        const response = await axios.post("/api/register", payload);

        if (response.data.checkout_url) {
            // Change status to "Processing..." right before redirect
            isSubmitting.value = false;
            isProcessingPayment.value = true;

            // Short delay to ensure the user sees the "Processing" state before the page leaves
            setTimeout(() => {
                window.location.href = response.data.checkout_url;
            }, 500);
        }
    } catch (error) {
        isProcessingPayment.value = false; // Reset if there's an error
        if (error.response && error.response.status === 422) {
            validationErrors.value = error.response.data.errors;
            submitError.value =
                "Validation failed. Please check the highlighted fields.";

            await nextTick();
            const firstError = document.querySelector(
                ".ring-red-500, .text-red-500"
            );
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            }
        } else {
            submitError.value =
                error.response?.data?.message || "An error occurred.";
        }
    } finally {
        // Only stop submitting if we aren't about to redirect
        if (!isProcessingPayment.value) {
            isSubmitting.value = false;
        }
    }
};

const showPrivacy = ref(false);
const canAgree = ref(false);
const agreeChecked = ref(false);
const policyBody = ref(null);

function openModal() {
    showPrivacy.value = true;
    canAgree.value = false;
}

function handleScroll() {
    const el = policyBody.value;
    if (!el) return;
    const bottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 10;
    if (bottom) canAgree.value = true;
}

function acceptPolicy() {
    agreeChecked.value = true;
    showPrivacy.value = false;
}
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" :href="logoIcon" />
    </Head>
    <div
        class="bg-[url('@/assets/bg.jpg')] bg-cover bg-center py-12 bg-no-repeat min-h-screen w-full grid place-items-center"
    >
        <div class="mx-auto w-full max-w-[1320px] px-5">
            <div class="flex flex-col md:flex-row justify-between">
                <div>
                    <!-- <img
                        src="@/assets/landing-cinco-logo.png"
                        class="mx-auto sm:h-[70px] h-auto sm:w-auto w-full pb-5"
                        alt=""
                    /> -->
                </div>

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

            <div class="text-[#DCDBE0] flex gap-5 mt-7 text-lg">
                <a href="/">BACK</a>
                <!-- <div>|</div>
                <a href="">MORE INFO</a> -->
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
                    class="font-gaming text-center md:pt-0 pt-2 text-white lg:text-7xl md:text-5xl text-3xl relative z-10"
                >
                    <i>REGISTRATION FORM</i>
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
                <div class="sm:flex grid items-center gap-4 relative z-10">
                    <h1
                        class="font-gaming text-white mx-auto sm:text-3xl text-2xl"
                    >
                        <i>TEAM NAME:</i>
                    </h1>
                    <div class="flex-1">
                        <input
                            type="text"
                            placeholder="Team Name (must be unique)"
                            v-model="teamName"
                            @input="handleInput('team.team_name')"
                            required
                            class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 rounded-md outline-none ring-2 ring-brand-blue"
                            :class="{
                                'ring-red-500': getError('team.team_name'),
                            }"
                        />
                        <p
                            v-if="getError('team.team_name')"
                            class="text-red-500 text-xs pt-1"
                        >
                            {{ getError("team.team_name") }}
                        </p>
                    </div>
                </div>

                <div class="relative z-10">
                    <h1
                        class="font-gaming text-white sm:text-3xl text-2xl text-center py-5"
                    >
                        <i>ADDRESS</i>
                    </h1>

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
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 text-center mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                    readonly
                                />
                            </div>
                        </div>

                        <div
                            class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                        >
                            <label class="text-white">Region</label>
                            <select
                                v-model="selectedRegion"
                                @change="handleInput('team.region')"
                                required
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                :class="{
                                    'ring-red-500': getError('team.region'),
                                }"
                            >
                                <option value="" disabled>Select Region</option>
                                <option
                                    v-for="r in regions"
                                    :key="r.code"
                                    :value="r.name"
                                >
                                    {{ r.name }}
                                </option>
                            </select>
                            <p
                                v-if="getError('team.region')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("team.region") }}
                            </p>
                        </div>

                        <div
                            class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                        >
                            <label class="text-white">Province</label>
                            <select
                                v-model="selectedProvince"
                                @change="handleInput('team.province')"
                                :disabled="isNcr || !selectedRegion"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] disabled:opacity-40"
                                :class="{
                                    'ring-red-500': getError('team.province'),
                                }"
                            >
                                <option value="" disabled>
                                    Select Province
                                </option>
                                <option
                                    v-for="p in provinces"
                                    :key="p.code"
                                    :value="p.name"
                                >
                                    {{ p.name }}
                                </option>
                            </select>
                            <p
                                v-if="getError('team.province')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("team.province") }}
                            </p>
                        </div>

                        <div
                            class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                        >
                            <label class="text-white">City</label>
                            <select
                                v-model="selectedCity"
                                @change="handleInput('team.city')"
                                :disabled="
                                    (!isNcr && !selectedProvince) ||
                                    cities.length === 0
                                "
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] disabled:opacity-40"
                                :class="{
                                    'ring-red-500': getError('team.city'),
                                }"
                            >
                                <option value="" disabled>Select City</option>
                                <option
                                    v-for="c in cities"
                                    :key="c.code"
                                    :value="c.name"
                                >
                                    {{ c.name }}
                                </option>
                            </select>
                            <p
                                v-if="getError('team.city')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("team.city") }}
                            </p>
                        </div>

                        <div
                            class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                        >
                            <label class="text-white">Barangay</label>
                            <select
                                v-model="selectedBarangay"
                                @change="handleInput('team.barangay')"
                                :disabled="!selectedCity"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] disabled:opacity-40"
                                :class="{
                                    'ring-red-500': getError('team.barangay'),
                                }"
                            >
                                <option value="" disabled>
                                    Select Barangay
                                </option>
                                <option
                                    v-for="b in barangays"
                                    :key="b.code"
                                    :value="b.name"
                                >
                                    {{ b.name }}
                                </option>
                            </select>
                            <p
                                v-if="getError('team.barangay')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("team.barangay") }}
                            </p>
                        </div>

                        <div
                            class="xl:col-span-2 md:col-span-4 sm:col-span-6 col-span-12"
                        >
                            <label class="text-white">Postal Code</label>
                            <input
                                type="text"
                                v-model="postalCode"
                                @input="handleInput('team.postal_code')"
                                placeholder="Postal Code"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                :class="{
                                    'ring-red-500':
                                        getError('team.postal_code'),
                                }"
                            />
                            <p
                                v-if="getError('team.postal_code')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("team.postal_code") }}
                            </p>
                        </div>
                    </div>

                    <h1
                        class="font-gaming text-white sm:text-3xl text-2xl text-center py-5"
                    >
                        <i>TEAM MEMBER'S</i>
                    </h1>

                    <div class="grid grid-cols-12 gap-4">
                        <template v-for="(p, index) in players" :key="index">
                            <div
                                class="md:col-span-3 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white"
                                    >(Player {{ index + 1 }}) Full Name</label
                                >
                                <input
                                    type="text"
                                    v-model="p.fullName"
                                    :name="'playerName' + index"
                                    placeholder="Full Name"
                                    required
                                    @input="
                                        handleInput(`details.${index}.fullName`)
                                    "
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                    :class="{
                                        'ring-red-500': getError(
                                            `details.${index}.fullName`,
                                            p.fullName
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.fullName`,
                                            p.fullName
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.fullName`,
                                            p.fullName
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="md:col-span-3 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white"
                                    >(IGN) In-Game Name</label
                                >
                                <input
                                    type="text"
                                    v-model="p.username"
                                    :name="'playerUsername' + index"
                                    placeholder="In-Game Name"
                                    required
                                    @input="
                                        handleInput(`details.${index}.username`)
                                    "
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                    :class="{
                                        'ring-red-500': getError(
                                            `details.${index}.username`,
                                            p.username,
                                            'username'
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.username`,
                                            p.username,
                                            'username'
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.username`,
                                            p.username,
                                            "username"
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="md:col-span-3 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">E-mail</label>
                                <input
                                    type="email"
                                    v-model="p.email"
                                    :name="'playerEmail' + index"
                                    placeholder="E-mail"
                                    required
                                    @input="
                                        handleInput(`details.${index}.email`)
                                    "
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                    :class="{
                                        'ring-red-500': getError(
                                            `details.${index}.email`,
                                            p.email,
                                            'email'
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.email`,
                                            p.email,
                                            'email'
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.email`,
                                            p.email,
                                            "email"
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="md:col-span-3 sm:col-span-6 col-span-12"
                            >
                                <label class="text-white">Mobile Number</label>
                                <input
                                    type="number"
                                    v-model="p.mobileNumber"
                                    :name="'playerNumber' + index"
                                    placeholder="Mobile Number"
                                    required
                                    @input="
                                        handleInput(
                                            `details.${index}.mobileNumber`
                                        )
                                    "
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                    :class="{
                                        'ring-red-500': getError(
                                            `details.${index}.mobileNumber`,
                                            p.mobileNumber,
                                            'mobile'
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.mobileNumber`,
                                            p.mobileNumber,
                                            'mobile'
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.mobileNumber`,
                                            p.mobileNumber,
                                            "mobile"
                                        )
                                    }}
                                </p>
                            </div>
                        </template>
                    </div>

                    <h1
                        class="font-gaming text-white sm:text-3xl text-2xl text-center py-5"
                    >
                        <i>Reserve Player (Optional)</i>
                    </h1>

                    <div class="grid grid-cols-12 gap-4">
                        <div class="md:col-span-3 sm:col-span-6 col-span-12">
                            <label class="text-white">Full Name</label>
                            <input
                                type="text"
                                v-model="reservePlayer.fullName"
                                placeholder="Full Name"
                                @input="handleInput('details.5.fullName')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                :class="{
                                    'ring-red-500':
                                        getError('details.5.fullName'),
                                }"
                            />
                            <p
                                v-if="getError('details.5.fullName')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("details.5.fullName") }}
                            </p>
                        </div>

                        <div class="md:col-span-3 sm:col-span-6 col-span-12">
                            <label class="text-white">(IGN) In-Game Name</label>
                            <input
                                type="text"
                                v-model="reservePlayer.username"
                                :required="!!reservePlayer.fullName"
                                placeholder="In-Game Name"
                                @input="handleInput('details.5.username')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                :class="{
                                    'ring-red-500':
                                        getError('details.5.username') ||
                                        getError(
                                            null,
                                            reservePlayer.username,
                                            'username'
                                        ),
                                }"
                            />
                            <p
                                v-if="
                                    getError('details.5.username') ||
                                    getError(
                                        null,
                                        reservePlayer.username,
                                        'username'
                                    )
                                "
                                class="text-red-500 text-xs pt-1"
                            >
                                {{
                                    getError("details.5.username") ||
                                    getError(
                                        null,
                                        reservePlayer.username,
                                        "username"
                                    )
                                }}
                            </p>
                        </div>

                        <div class="md:col-span-3 sm:col-span-6 col-span-12">
                            <label class="text-white">E-mail</label>
                            <input
                                type="email"
                                v-model="reservePlayer.email"
                                :required="!!reservePlayer.fullName"
                                placeholder="E-mail"
                                @input="handleInput('details.5.email')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                :class="{
                                    'ring-red-500':
                                        getError('details.5.email') ||
                                        getError(
                                            null,
                                            reservePlayer.email,
                                            'email'
                                        ),
                                }"
                            />
                            <p
                                v-if="
                                    getError('details.5.email') ||
                                    getError(null, reservePlayer.email, 'email')
                                "
                                class="text-red-500 text-xs pt-1"
                            >
                                {{
                                    getError("details.5.email") ||
                                    getError(null, reservePlayer.email, "email")
                                }}
                            </p>
                        </div>

                        <div class="md:col-span-3 sm:col-span-6 col-span-12">
                            <label class="text-white">Mobile Number</label>
                            <input
                                type="number"
                                v-model="reservePlayer.mobileNumber"
                                :required="!!reservePlayer.fullName"
                                placeholder="Mobile Number"
                                @input="handleInput('details.5.mobileNumber')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                :class="{
                                    'ring-red-500':
                                        getError('details.5.mobileNumber') ||
                                        getError(
                                            null,
                                            reservePlayer.mobileNumber,
                                            'mobile'
                                        ),
                                }"
                            />
                            <p
                                v-if="
                                    getError('details.5.mobileNumber') ||
                                    getError(
                                        null,
                                        reservePlayer.mobileNumber,
                                        'mobile'
                                    )
                                "
                                class="text-red-500 text-xs pt-1"
                            >
                                {{
                                    getError("details.5.mobileNumber") ||
                                    getError(
                                        null,
                                        reservePlayer.mobileNumber,
                                        "mobile"
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <template v-if="additionalShirtCount > 0">
                        <h1
                            class="font-gaming text-white sm:text-3xl text-2xl text-center py-5"
                        >
                            <i>Availing Shirt Details</i>
                        </h1>

                        <div class="grid grid-cols-12 gap-4">
                            <template
                                v-for="(s, index) in availShirts"
                                :key="index"
                            >
                                <div
                                    class="md:col-span-4 sm:col-span-6 col-span-12"
                                >
                                    <label class="text-white"
                                        >(Shirt {{ index + 1 }}) Full
                                        Name</label
                                    >
                                    <input
                                        type="text"
                                        v-model="s.fullName"
                                        :name="'availName' + index"
                                        placeholder="Full Name"
                                        required
                                        @input="
                                            handleInput(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.fullName`
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                        :class="{
                                            'ring-red-500': getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.fullName`,
                                                s.fullName
                                            ),
                                        }"
                                    />
                                    <input type="hidden" v-model="s.username" />
                                    <p
                                        v-if="
                                            getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.fullName`,
                                                s.fullName
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.fullName`,
                                                s.fullName
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="md:col-span-4 sm:col-span-6 col-span-12"
                                >
                                    <label class="text-white">E-mail</label>
                                    <input
                                        type="email"
                                        v-model="s.email"
                                        :name="'availEmail' + index"
                                        placeholder="E-mail"
                                        required
                                        @input="
                                            handleInput(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.email`
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                        :class="{
                                            'ring-red-500': getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.email`,
                                                s.email,
                                                'email'
                                            ),
                                        }"
                                    />
                                    <p
                                        v-if="
                                            getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.email`,
                                                s.email,
                                                'email'
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.email`,
                                                s.email,
                                                "email"
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="md:col-span-4 sm:col-span-6 col-span-12"
                                >
                                    <label class="text-white"
                                        >Mobile Number</label
                                    >
                                    <input
                                        type="number"
                                        v-model="s.mobileNumber"
                                        :name="'availNumber' + index"
                                        placeholder="Mobile Number"
                                        required
                                        @input="
                                            handleInput(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.mobileNumber`
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                        :class="{
                                            'ring-red-500': getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.mobileNumber`,
                                                s.mobileNumber,
                                                'mobile'
                                            ),
                                        }"
                                    />
                                    <p
                                        v-if="
                                            getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.mobileNumber`,
                                                s.mobileNumber,
                                                'mobile'
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${
                                                    INITIAL_PLAYER_COUNT + index
                                                }.mobileNumber`,
                                                s.mobileNumber,
                                                "mobile"
                                            )
                                        }}
                                    </p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- =============== PRIVACY CHECKBOX =============== -->
                    <div class="flex flex-col pt-5">
                        <div class="flex items-center gap-3">
                            <input
                                id="agree"
                                type="checkbox"
                                v-model="agreeChecked"
                                @change="handleInput('team.agree')"
                                class="mt-1 h-4 w-4 text-brand focus:ring-brand border-gray-300 rounded"
                                :class="{
                                    'ring-2 ring-red-500':
                                        getError('team.agree'),
                                }"
                            />

                            <label for="agree" class="text-sm text-white">
                                I agree to the
                                <a
                                    href="#"
                                    class="text-brand-green font-bold hover:underline"
                                    @click.prevent="openModal"
                                >
                                    Privacy Policy
                                </a>
                            </label>
                        </div>

                        <p
                            v-if="getError('team.agree')"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ getError("team.agree") }}
                        </p>
                    </div>

                    <!-- =============== PRIVACY POLICY MODAL =============== -->
                    <div
                        v-if="showPrivacy"
                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                    >
                        <div
                            class="bg-[url('@/assets/nbg3.jpg')] border-4 border-brand-blue bg-cover bg-no-repeat rounded-xl w-dull max-w-2xl shadow-xl overflow-hidden"
                        >
                            <!-- Scrollable Content -->
                            <div
                                ref="policyBody"
                                @scroll="handleScroll"
                                class="py-6 max-h-[60vh] overflow-y-auto text-gray-700 space-y-4 text-sm"
                            >
                                <PrivacyPolicy />
                            </div>

                            <!-- Modal Footer -->
                            <div class="p-4 border-t flex justify-end gap-3">
                                <button
                                    class="px-4 py-2 rounded-md bg-gray-300"
                                    @click="showPrivacy = false"
                                >
                                    Close
                                </button>

                                <!-- Agree button becomes visible only after scrolling -->
                                <button
                                    v-if="canAgree"
                                    class="px-4 py-2 rounded-md bg-green-600 text-white"
                                    @click="acceptPolicy"
                                >
                                    Agree
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="xl:flex grid xl:justify-between justify-center mt-4"
                    >
                        <div>
                            <h1
                                class="font-gaming text-white sm:text-3xl text-2xl py-5"
                            >
                                <i>PAYMENT:</i>
                            </h1>
                        </div>

                        <div class="">
                            <div class="grid grid-cols-6 gap-4">
                                <div class="sm:col-span-2 col-span-6">
                                    <label for="" class="text-white text-[14px]"
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
                                                class="bg-brand-blue text-white px-3 py-0 font-bold text-2xl disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                -
                                            </button>
                                            <button
                                                type="button"
                                                @click="incrementShirt"
                                                class="bg-brand-blue text-white px-2 py-0 font-bold text-2xl"
                                            >
                                                +
                                            </button>
                                        </div>

                                        <input
                                            type="text"
                                            :value="additionalShirtCount"
                                            readonly
                                            class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-brand-blue"
                                        />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 col-span-6">
                                    <label for="" class="text-white text-[14px]"
                                        >Total</label
                                    >
                                    <input
                                        type="text"
                                        :value="`₱ ${totalPayment.toLocaleString(
                                            'en-PH',
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            }
                                        )}`"
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-green-register"
                                        readonly
                                    />
                                </div>

                                <div class="sm:col-span-2 col-span-6">
                                    <button
                                        type="submit"
                                        @click.prevent="registerTeam"
                                        :disabled="
                                            isSubmitting || isProcessingPayment
                                        "
                                        class="sm:py-[3px] py-[5px] sm:mt-[23px] mt-4 w-full text-2xl bg-green-register text-white rounded-md border-2 border-white disabled:opacity-50"
                                    >
                                        {{
                                            isSubmitting
                                                ? "Submitting..."
                                                : isProcessingPayment
                                                ? "Processing..."
                                                : "Pay Now"
                                        }}
                                    </button>
                                </div>
                            </div>

                            <p
                                v-if="submitError"
                                class="text-red-500 text-sm pt-2 whitespace-pre-line"
                            >
                                {{ submitError }}
                            </p>
                            <p
                                v-else-if="submitMessage"
                                class="text-green-500 text-sm pt-2"
                            >
                                {{ submitMessage }}
                            </p>

                            <p
                                class="text-[#d8d4d4] xl:text-start text-center text-sm pt-2"
                            >
                                Note: Every add on shirt is worth ₱ 500.00 (Any
                                add on shirt does not mean they can play)
                                <span class="hidden sm:inline">
                                    <br />
                                </span>
                                (The 5 listed player's automatically has Shirt
                                upon Registration, costing
                                {{
                                    BASE_REGISTRATION_FEE.toLocaleString(
                                        "en-PH",
                                        { minimumFractionDigits: 2 }
                                    )
                                }})
                                <span class="block mt-1 italic text-[#f3f3f3]">
                                    * Reserve players do not have a shirt
                                    included, they will only receive one and be
                                    eligible to play if a main player backs out.
                                </span>
                            </p>
                        </div>
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
    font-weight: normal;
    font-style: normal;
}

.font-gaming {
    font-family: "GamingSporty", sans-serif;
}
</style>
