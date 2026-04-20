<script setup>
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { useAddress } from "@/composables/useAddress";
import PrivacyPolicy from "@/components/privacy-policy/Privacy-policy.vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import logoIcon from "@/assets/logo.png";

const SHIRT_PRICE = 600;
const INITIAL_PLAYER_COUNT = 5;
const BASE_REGISTRATION_FEE = (INITIAL_PLAYER_COUNT + 1) * SHIRT_PRICE;
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
    isNcr,
} = useAddress();

const teamName = ref("");
const postalCode = ref("");
const players = ref([]);

const reservePlayer = ref({
    fullName: "",
    username: "",
    email: "",
    mobileNumber: "",
    accountType: "Reserve",
    size_shirt: "",
});

const additionalShirtCount = ref(0);
const availShirtDetails = ref([]);

const isSubmitting = ref(false);
const isProcessingPayment = ref(false);
const submitError = ref(null);
const validationErrors = ref({});

const isSuccessfullySubmitted = ref(false);

// Privacy Policy States
const showPrivacy = ref(false);
const canAgree = ref(false);
const agreeChecked = ref(false);
const policyBody = ref(null);

const availShirts = computed(() =>
    Array.from({ length: additionalShirtCount.value }, (_, index) => {
        if (!availShirtDetails.value[index]) {
            availShirtDetails.value[index] = {
                fullName: "",
                username: "N/A",
                email: "",
                mobileNumber: "",
                accountType: "Shirt",
                size_shirt: "",
            };
        }
        return availShirtDetails.value[index];
    }),
);

const totalPayment = computed(
    () => BASE_REGISTRATION_FEE + additionalShirtCount.value * SHIRT_PRICE,
);

/**
 * FIXED: Prevent refresh/exit lock
 * We handle the window lock here.
 */
watch(
    [isSubmitting, isProcessingPayment, isSuccessfullySubmitted],
    ([submitting, processing, success]) => {
        // If we have successfully submitted, kill the "Are you sure you want to leave"
        // immediately so the redirect works without a popup.
        if (success) {
            window.onbeforeunload = null;
            return;
        }

        if (submitting || processing) {
            window.onbeforeunload = (e) => {
                e.preventDefault();
                return "Registration is in progress...";
            };
        } else {
            window.onbeforeunload = null;
        }
    },
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
                size_shirt: "",
            }));
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
        size_shirt: "",
    }));
    reservePlayer.value = {
        fullName: "",
        username: "",
        email: "",
        mobileNumber: "",
        accountType: "Reserve",
        size_shirt: "",
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
            reservePlayer: reservePlayer.value,
            additional_shirt_count: additionalShirtCount.value,
            availShirtDetails: availShirtDetails.value,
        }),
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
    { deep: true },
);

const allUsers = computed(() => [
    ...players.value,
    reservePlayer.value,
    ...availShirtDetails.value,
]);

const duplicateEmails = computed(() => {
    const emails = allUsers.value.map((u) => u.email?.trim().toLowerCase());
    return emails.filter((email, i) => email && emails.indexOf(email) !== i);
});

const duplicateMobiles = computed(() => {
    const mobiles = allUsers.value.map((u) =>
        String(u.mobileNumber || "").trim(),
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
    if (validationErrors.value && validationErrors.value[fieldName])
        delete validationErrors.value[fieldName];
    if (Object.keys(validationErrors.value).length === 0)
        submitError.value = null;
};

const getError = (fieldName, value = null, type = null) => {
    if (fieldName && validationErrors.value[fieldName])
        return validationErrors.value[fieldName][0];
    if (
        type === "email" &&
        value &&
        duplicateEmails.value.includes(value.trim().toLowerCase())
    )
        return "Duplicate email detected";
    if (type === "mobile" && value) {
        const valStr = String(value).trim();
        if (duplicateMobiles.value.includes(valStr))
            return "Duplicate mobile number detected";
        if (valStr.length > 0 && valStr.length !== 10)
            return "Mobile number must be 10 digits";
    }
    if (
        type === "username" &&
        value &&
        value.toLowerCase() !== "n/a" &&
        duplicateUsernames.value.includes(value.trim().toLowerCase())
    )
        return "Duplicate IGN/Username detected";
    if (type === "size_shirt" && (!value || value === ""))
        return validationErrors.value[fieldName]
            ? "Please select a shirt size"
            : null;
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
    submitError.value = null;
    validationErrors.value = {};

    if (!agreeChecked.value) {
        submitError.value = "You must agree to the Privacy Policy to proceed.";
        return;
    }

    // Start Loading
    isSubmitting.value = true;

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
        details: [
            ...players.value,
            reservePlayer.value,
            ...availShirtDetails.value,
        ].map((u) => ({
            fullName: u.fullName,
            username: u.accountType === "Shirt" ? null : u.username || "",
            email: u.email,
            mobileNumber: String(u.mobileNumber),
            accountType: u.accountType,
            size_shirt: u.size_shirt,
        })),
    };

    try {
        const response = await axios.post("/api/register", payload);

        if (response.data.token) {
            // 1. Mark success to unlock the "onbeforeunload" prompt
            isSuccessfullySubmitted.value = true;
            window.onbeforeunload = null;

            // 2. Clear draft
            localStorage.removeItem(LOCAL_STORAGE_KEY);

            // NOTE: We do NOT set isSubmitting.value = false here.
            // This keeps the loading screen visible until the browser
            // actually leaves this page for the success URL.

            window.location.replace(
                `/payment/success?token=${response.data.token}`,
            );
        }
    } catch (error) {
        // If it fails, we STOP loading so the user can fix the form
        isSubmitting.value = false;
        isProcessingPayment.value = false;

        if (error.response && error.response.status === 422) {
            validationErrors.value = error.response.data.errors;
            submitError.value =
                "Validation failed. Please check the highlighted fields.";
        } else {
            submitError.value =
                error.response?.data?.message || "An error occurred.";
        }
    }
};

function openModal() {
    showPrivacy.value = true;
    canAgree.value = false;
}
function handleScroll() {
    const el = policyBody.value;
    if (el && el.scrollTop + el.clientHeight >= el.scrollHeight - 10)
        canAgree.value = true;
}
function acceptPolicy() {
    agreeChecked.value = true;
    showPrivacy.value = false;
    if (validationErrors.value["team.agree"])
        delete validationErrors.value["team.agree"];
}
</script>

<template>
    <Head>
        <link rel="icon" type="image/png" :href="logoIcon" />
    </Head>
    <div
        class="bg-[url('@/assets/bg.jpg')] bg-cover bg-center py-12 bg-no-repeat bg-fixed min-h-screen w-full grid place-items-center"
    >
        <div class="mx-auto w-full max-w-[1500px]">
            <div
                class="flex flex-col gap-5 xl:flex-row justify-between items-center"
            >
                <div class="mt-4 relative w-full max-w-[650px] px-5">
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
                            class="font-gaming text-center md:pt-0 pt-2 text-white text-lg relative z-10"
                        >
                            Registration starts NOW until April 30, 2026
                        </h1>
                    </div>
                </div>

                <div
                    class="flex gap-5 flex-col sm:flex-row items-center justify-between"
                >
                    <p class="text-white text-lg">In Cooperation with:</p>
                    <img
                        src="@/assets/landing-bb88-logo.png"
                        class="sm:h-[46px] h-auto sm:w-auto w-full"
                        alt=""
                    />
                </div>
            </div>

            <div class="text-[#DCDBE0] flex gap-5 mt-7 px-5 sm:text-lg text-md">
                <a href="/">HOME</a>
                <div>|</div>
                <div>
                    REGISTRATION STEP-BY-STEP
                    <a href="register/how" class="underline text-brand-blue"
                        >GUIDE HERE</a
                    >
                </div>
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
                    <div class="py-5 text-center">
                        <h1 class="font-gaming text-white sm:text-3xl text-2xl">
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
                                :disabled="!selectedRegion || isNcr"
                                required
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2 ring-[#bf38a6] disabled:opacity-50"
                                :class="{
                                    'ring-red-500': getError('team.province'),
                                }"
                            >
                                <option v-if="!isNcr" value="" disabled>
                                    Select Province
                                </option>

                                <option v-if="isNcr" value="N/A">N/A</option>

                                <option
                                    v-else
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
                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                    ></div>

                    <div class="py-5 text-center">
                        <h1 class="font-gaming text-white sm:text-3xl text-2xl">
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
                                            p.fullName,
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.fullName`,
                                            p.fullName,
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.fullName`,
                                            p.fullName,
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="xl:col-span-2 md:col-span-4 col-span-12"
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
                                            'username',
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.username`,
                                            p.username,
                                            'username',
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.username`,
                                            p.username,
                                            "username",
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="xl:col-span-3 md:col-span-4 col-span-12"
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
                                            'email',
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.email`,
                                            p.email,
                                            'email',
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.email`,
                                            p.email,
                                            "email",
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="xl:col-span-3 md:col-span-6 col-span-12"
                            >
                                <label class="text-white">Mobile Number</label>
                                <input
                                    type="number"
                                    v-model="p.mobileNumber"
                                    :name="'playerNumber' + index"
                                    placeholder="9123123123"
                                    required
                                    @input="
                                        handleInput(
                                            `details.${index}.mobileNumber`,
                                        )
                                    "
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                    :class="{
                                        'ring-red-500': getError(
                                            `details.${index}.mobileNumber`,
                                            p.mobileNumber,
                                            'mobile',
                                        ),
                                    }"
                                />
                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.mobileNumber`,
                                            p.mobileNumber,
                                            'mobile',
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.mobileNumber`,
                                            p.mobileNumber,
                                            "mobile",
                                        )
                                    }}
                                </p>
                            </div>

                            <div
                                class="xl:col-span-2 md:col-span-6 col-span-12"
                            >
                                <label class="text-white">Shirt Size</label>
                                <select
                                    v-model="p.size_shirt"
                                    required
                                    @change="
                                        handleInput(
                                            `details.${index}.size_shirt`,
                                        )
                                    "
                                    class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2"
                                    :class="
                                        getError(`details.${index}.size_shirt`)
                                            ? 'ring-red-500'
                                            : 'ring-[#bf38a6]'
                                    "
                                >
                                    <option value="" disabled selected>
                                        Select Size
                                    </option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                </select>

                                <p
                                    v-if="
                                        getError(
                                            `details.${index}.size_shirt`,
                                            p.size_shirt,
                                            'size_shirt',
                                        )
                                    "
                                    class="text-red-500 text-xs pt-1"
                                >
                                    {{
                                        getError(
                                            `details.${index}.size_shirt`,
                                            p.mobileNumber,
                                            "size_shirt",
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </template>
                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                    ></div>

                    <div class="py-5 text-center">
                        <h1 class="font-gaming text-white sm:text-3xl text-2xl">
                            <i>RESERVE PLAYER</i>
                        </h1>

                        <p class="text-[#d8d4d4] text-sm">
                            Make sure the reserve player details are correct, as
                            they cannot be edited once submitted.
                        </p>
                    </div>

                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                    ></div>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="xl:col-span-2 md:col-span-4 col-span-12">
                            <label class="text-white"
                                ><span class="font-bold">(Reserve)</span> Full
                                Name</label
                            >
                            <input
                                type="text"
                                v-model="reservePlayer.fullName"
                                required
                                placeholder="Full Name"
                                @input="handleInput('details.5.fullName')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2"
                                :class="
                                    getError('details.5.fullName')
                                        ? 'ring-red-500'
                                        : 'ring-[#bf38a6]'
                                "
                            />
                            <p
                                v-if="getError('details.5.fullName')"
                                class="text-red-500 text-xs pt-1"
                            >
                                {{ getError("details.5.fullName") }}
                            </p>
                        </div>

                        <div class="xl:col-span-2 md:col-span-4 col-span-12">
                            <label class="text-white">In-Game Name</label>
                            <input
                                type="text"
                                v-model="reservePlayer.username"
                                required
                                placeholder="In-Game Name"
                                @input="handleInput('details.5.username')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2"
                                :class="
                                    getError('details.5.username') ||
                                    getError(
                                        null,
                                        reservePlayer.username,
                                        'username',
                                    )
                                        ? 'ring-red-500'
                                        : 'ring-[#bf38a6]'
                                "
                            />
                            <p
                                v-if="
                                    getError('details.5.username') ||
                                    getError(
                                        null,
                                        reservePlayer.username,
                                        'username',
                                    )
                                "
                                class="text-red-500 text-xs pt-1"
                            >
                                {{
                                    getError("details.5.username") ||
                                    getError(
                                        null,
                                        reservePlayer.username,
                                        "username",
                                    )
                                }}
                            </p>
                        </div>

                        <div class="xl:col-span-3 md:col-span-4 col-span-12">
                            <label class="text-white">E-mail</label>
                            <input
                                type="email"
                                v-model="reservePlayer.email"
                                required
                                placeholder="E-mail"
                                @input="handleInput('details.5.email')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2"
                                :class="
                                    getError('details.5.email') ||
                                    getError(null, reservePlayer.email, 'email')
                                        ? 'ring-red-500'
                                        : 'ring-[#bf38a6]'
                                "
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

                        <div class="xl:col-span-3 md:col-span-6 col-span-12">
                            <label class="text-white">Mobile Number</label>
                            <input
                                type="number"
                                v-model="reservePlayer.mobileNumber"
                                required
                                placeholder="9123123123"
                                @input="handleInput('details.5.mobileNumber')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2"
                                :class="
                                    getError('details.5.mobileNumber') ||
                                    getError(
                                        null,
                                        reservePlayer.mobileNumber,
                                        'mobile',
                                    )
                                        ? 'ring-red-500'
                                        : 'ring-[#bf38a6]'
                                "
                            />
                            <p
                                v-if="
                                    getError('details.5.mobileNumber') ||
                                    getError(
                                        null,
                                        reservePlayer.mobileNumber,
                                        'mobile',
                                    )
                                "
                                class="text-red-500 text-xs pt-1"
                            >
                                {{
                                    getError("details.5.mobileNumber") ||
                                    getError(
                                        null,
                                        reservePlayer.mobileNumber,
                                        "mobile",
                                    )
                                }}
                            </p>
                        </div>

                        <div class="xl:col-span-2 md:col-span-6 col-span-12">
                            <label class="text-white">Shirt Size</label>
                            <select
                                v-model="reservePlayer.size_shirt"
                                required
                                @change="handleInput('details.5.size_shirt')"
                                class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2"
                                :class="
                                    getError(
                                        'details.5.size_shirt',
                                        reservePlayer.size_shirt,
                                        'size_shirt',
                                    )
                                        ? 'ring-red-500'
                                        : 'ring-[#bf38a6]'
                                "
                            >
                                <option value="" disabled>Select Size</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>

                            <p
                                v-if="
                                    getError(
                                        'details.5.size_shirt',
                                        reservePlayer.size_shirt,
                                        'size_shirt',
                                    )
                                "
                                class="text-red-500 text-xs pt-1"
                            >
                                {{
                                    getError(
                                        "details.5.size_shirt",
                                        reservePlayer.size_shirt,
                                        "size_shirt",
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                    ></div>

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
                                        Full Name</label
                                    >
                                    <input
                                        type="text"
                                        v-model="s.fullName"
                                        :name="'availName' + index"
                                        placeholder="Full Name"
                                        required
                                        @input="
                                            handleInput(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.fullName`,
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                        :class="{
                                            'ring-red-500': getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.fullName`,
                                                s.fullName,
                                            ),
                                        }"
                                    />
                                    <input type="hidden" v-model="s.username" />
                                    <p
                                        v-if="
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.fullName`,
                                                s.fullName,
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.fullName`,
                                                s.fullName,
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
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
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.email`,
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                        :class="{
                                            'ring-red-500': getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.email`,
                                                s.email,
                                                'email',
                                            ),
                                        }"
                                    />
                                    <p
                                        v-if="
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.email`,
                                                s.email,
                                                'email',
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.email`,
                                                s.email,
                                                "email",
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white"
                                        >Mobile Number</label
                                    >
                                    <input
                                        type="number"
                                        v-model="s.mobileNumber"
                                        :name="'availNumber' + index"
                                        placeholder="9123123123"
                                        required
                                        @input="
                                            handleInput(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.mobileNumber`,
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full p-2 mt-1 rounded-md outline-none ring-2 ring-[#bf38a6]"
                                        :class="{
                                            'ring-red-500': getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.mobileNumber`,
                                                s.mobileNumber,
                                                'mobile',
                                            ),
                                        }"
                                    />
                                    <p
                                        v-if="
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.mobileNumber`,
                                                s.mobileNumber,
                                                'mobile',
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.mobileNumber`,
                                                s.mobileNumber,
                                                "mobile",
                                            )
                                        }}
                                    </p>
                                </div>

                                <div
                                    class="xl:col-span-3 md:col-span-6 col-span-12"
                                >
                                    <label class="text-white">Shirt Size</label>
                                    <select
                                        v-model="
                                            availShirtDetails[index].size_shirt
                                        "
                                        required
                                        @change="
                                            handleInput(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.size_shirt`,
                                            )
                                        "
                                        class="bg-[rgba(0,0,0,0.7)] text-white w-full px-2 py-[11px] mt-1 rounded-md outline-none ring-2"
                                        :class="
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.size_shirt`,
                                                availShirtDetails[index]
                                                    .size_shirt,
                                                'size_shirt',
                                            )
                                                ? 'ring-red-500'
                                                : 'ring-[#bf38a6]'
                                        "
                                    >
                                        <option value="" disabled>
                                            Select Size
                                        </option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                    </select>

                                    <p
                                        v-if="
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.size_shirt`,
                                                availShirtDetails[index]
                                                    .size_shirt,
                                                'size_shirt',
                                            )
                                        "
                                        class="text-red-500 text-xs pt-1"
                                    >
                                        {{
                                            getError(
                                                `details.${INITIAL_PLAYER_COUNT + 1 + index}.size_shirt`,
                                                availShirtDetails[index]
                                                    .size_shirt,
                                                "size_shirt",
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </template>
                        <div
                            class="w-full bg-slate-100 opacity-50 h-px mt-5 mb-4"
                        ></div>
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
                                            },
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
                                        class="sm:py-[3px] py-[5px] sm:mt-[23px] mt-4 w-full text-2xl bg-green-register text-white rounded-md border-2 border-white disabled:opacity-50 transition-all duration-200 active:scale-95"
                                    >
                                        {{
                                            isSubmitting
                                                ? "Submitting..."
                                                : isProcessingPayment
                                                  ? "Redirecting..."
                                                  : "Submit"
                                        }}
                                    </button>
                                </div>

                                <Teleport to="body">
                                    <div
                                        v-if="
                                            isSubmitting || isProcessingPayment
                                        "
                                        class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-black/80 backdrop-blur-sm"
                                    >
                                        <div
                                            class="relative flex items-center justify-center mb-6"
                                        >
                                            <div
                                                class="w-20 h-20 border-4 border-brand-pink/20 border-t-brand-pink rounded-full animate-spin"
                                            ></div>
                                            <img
                                                :src="logoIcon"
                                                class="absolute w-10 h-10 object-contain"
                                                alt="Logo"
                                            />
                                        </div>

                                        <h2
                                            class="text-white text-2xl font-gaming italic tracking-widest animate-pulse"
                                        >
                                            {{
                                                isSubmitting
                                                    ? "REGISTERING TEAM..."
                                                    : "PREPARING SUCCESS PAGE..."
                                            }}
                                        </h2>
                                        <p
                                            class="text-gray-400 mt-2 text-sm uppercase tracking-tighter"
                                        >
                                            Please do not close this window or
                                            refresh the page.
                                        </p>
                                    </div>
                                </Teleport>
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

                                <br />
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
