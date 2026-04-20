<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    team: Object,
});

const isProcessing = ref(false);
const showConfirmModal = ref(false);

// QR Modal State
const selectedIndex = ref(null);

// Helper for currency formatting
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-PH", {
        minimumFractionDigits: 2,
    }).format(value);
};

// Check if payment button should be shown
const showPaymentButton = computed(() => {
    return props.team.transaction_status !== "paid";
});

// Status badge styling
const statusClasses = computed(() => {
    const status = props.team.transaction_status;
    if (status === "paid")
        return "bg-green-400/10 text-green-400 border-green-400/20";
    if (status === "failed")
        return "bg-red-400/10 text-red-400 border-red-400/20";
    return "bg-yellow-400/10 text-yellow-400 border-yellow-400/20";
});

// QR Modal Functions
const openQRModal = (index) => {
    selectedIndex.value = index;
};

const closeQRModal = () => {
    selectedIndex.value = null;
};

const nextQR = (e) => {
    e.stopPropagation(); // Prevent modal from closing
    if (selectedIndex.value < props.team.detail_user.length - 1) {
        selectedIndex.value++;
    } else {
        selectedIndex.value = 0; // Loop back to start
    }
};

const prevQR = (e) => {
    e.stopPropagation(); // Prevent modal from closing
    if (selectedIndex.value > 0) {
        selectedIndex.value--;
    } else {
        selectedIndex.value = props.team.detail_user.length - 1; // Loop to end
    }
};

// Computed for the current member in modal
const currentModalMember = computed(() => {
    if (selectedIndex.value === null) return null;
    return props.team.detail_user[selectedIndex.value];
});

// Function to trigger the payment modal
const confirmPayment = () => {
    if (isProcessing.value) return;
    showConfirmModal.value = true;
};

// Function that actually executes the payment
const processPayment = () => {
    showConfirmModal.value = false;
    isProcessing.value = true;

    router.post(
        "/player/payment",
        {},
        {
            onStart: () => {
                isProcessing.value = true;
            },
            onError: () => {
                isProcessing.value = false;
            },
        },
    );
};
</script>

<template>
    <div class="text-white space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold">Team Dashboard</h1>
                <p class="text-brand-gray text-sm">
                    Manage your team registration and view payment status.
                </p>
            </div>

            <div v-if="showPaymentButton">
                <button
                    @click="confirmPayment"
                    :disabled="isProcessing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i
                        v-if="isProcessing"
                        class="fa-solid fa-spinner animate-spin"
                    ></i>
                    <i v-else class="fa-solid fa-credit-card"></i>
                    <span v-if="isProcessing">Redirecting...</span>
                    <span v-else
                        >Pay Now ₱{{ formatCurrency(team.total_payment) }}</span
                    >
                </button>
            </div>
            <div
                v-else
                class="flex items-center gap-2 text-green-400 font-bold bg-green-400/10 px-4 py-2 rounded-xl border border-green-400/20"
            >
                <i class="fa-solid fa-circle-check"></i>
                Registration Fully Paid
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-[#162236] p-6 rounded-2xl border border-brand-border-black shadow-sm"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <p
                            class="text-brand-gray text-sm font-medium uppercase tracking-wider"
                        >
                            Team Name
                        </p>
                        <h3 class="text-2xl font-bold mt-1">
                            {{ team.team_name }}
                        </h3>
                    </div>
                    <div class="bg-brand-dark-black p-3 rounded-xl">
                        <i class="fa-solid fa-shield-halved text-blue-400"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-[#162236] p-6 rounded-2xl border border-brand-border-black shadow-sm"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <p
                            class="text-brand-gray text-sm font-medium uppercase tracking-wider"
                        >
                            Payment Status
                        </p>
                        <div
                            :class="statusClasses"
                            class="inline-block px-2 py-1 rounded border text-[10px] uppercase font-bold mt-2"
                        >
                            {{
                                team.transaction_status?.replace("_", " ") ||
                                "Unknown"
                            }}
                        </div>
                    </div>
                    <div class="bg-brand-dark-black p-3 rounded-xl">
                        <i
                            class="fa-solid fa-file-invoice-dollar text-yellow-400"
                        ></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-[#162236] p-6 rounded-2xl border border-brand-border-black shadow-sm"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <p
                            class="text-brand-gray text-sm font-medium uppercase tracking-wider"
                        >
                            Total Amount
                        </p>
                        <h3 class="text-2xl font-bold mt-1 text-white">
                            ₱{{ formatCurrency(team.total_payment) }}
                        </h3>
                    </div>
                    <div class="bg-brand-dark-black p-3 rounded-xl">
                        <i class="fa-solid fa-coins text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="bg-[#162236] rounded-2xl p-4 md:p-8 border border-brand-border-black min-h-[300px]"
        >
            <h2
                class="text-lg font-semibold mb-4 border-b border-brand-border-black pb-2 flex items-center gap-2"
            >
                <i class="fa-solid fa-users text-blue-400"></i>
                Team Members
            </h2>

            <div class="w-full">
                <div
                    class="hidden md:grid md:grid-cols-5 text-brand-gray text-sm uppercase tracking-wider font-medium pb-4 px-2"
                >
                    <div>Name / Username</div>
                    <div class="text-center">Shirt Size</div>
                    <div class="text-center">Type</div>
                    <div class="text-center">QR Code</div>
                    <div class="text-right">Claim Shirt Status</div>
                </div>

                <div
                    class="space-y-4 md:space-y-0 divide-y md:divide-y divide-brand-border-black"
                >
                    <div
                        v-for="(member, index) in team.detail_user"
                        :key="member.id"
                        class="flex flex-col md:grid md:grid-cols-5 py-4 gap-3 md:gap-0 hover:bg-white/5 transition-colors px-2 rounded-xl md:rounded-none"
                    >
                        <div class="flex justify-between md:block">
                            <div>
                                <span
                                    class="md:hidden text-brand-gray text-xs uppercase font-bold"
                                    >Member</span
                                >
                            </div>
                            <div class="text-right md:text-left">
                                <div
                                    class="font-bold text-white text-sm md:text-base"
                                >
                                    {{ member.full_name }}
                                </div>
                                <div class="text-xs text-brand-gray">
                                    @{{ member.username }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex justify-between items-center md:justify-center"
                        >
                            <span
                                class="md:hidden text-brand-gray text-xs uppercase font-bold"
                                >Shirt Size</span
                            >
                            <div
                                class="bg-brand-dark-black px-3 py-1 rounded-lg border border-white/5 w-[80px] text-center"
                            >
                                <span
                                    class="text-xs text-white font-mono uppercase"
                                    >{{ member.size_shirt }}</span
                                >
                            </div>
                        </div>

                        <div
                            class="flex justify-between md:justify-center items-center"
                        >
                            <span
                                class="md:hidden text-brand-gray text-xs uppercase font-bold"
                                >Type</span
                            >
                            <span class="text-sm text-brand-gray">{{
                                member.account_type
                            }}</span>
                        </div>

                        <div
                            class="flex justify-between md:justify-center items-center"
                        >
                            <span
                                class="md:hidden text-brand-gray text-xs uppercase font-bold"
                                >QR Code</span
                            >

                            <template v-if="team.transaction_status === 'paid'">
                                <img
                                    :src="'/qr_image/' + member.qrcode_img"
                                    class="w-28 h-28 cursor-pointer hover:scale-105 transition-transform rounded shadow-lg"
                                    @click="openQRModal(index)"
                                />
                            </template>
                            <div
                                v-else
                                class="w-28 h-28 bg-gray-800/50 flex flex-col items-center justify-center rounded border-2 border-dashed border-white/10 text-brand-gray"
                            >
                                <span
                                    class="text-[10px] uppercase font-bold mb-1"
                                    >Locked</span
                                >
                                <i class="fa-solid fa-lock text-xl"></i>
                            </div>
                        </div>

                        <div
                            class="flex justify-between md:justify-end items-center"
                        >
                            <span
                                class="md:hidden text-brand-gray text-xs uppercase font-bold"
                                >Status</span
                            >
                            <span
                                v-if="member.status === 'claimed'"
                                class="text-blue-400 bg-blue-400/10 border border-blue-400/20 px-2 py-1 rounded text-[10px] uppercase font-bold"
                                >Claimed</span
                            >
                            <span
                                v-else
                                class="bg-yellow-400/10 text-yellow-400 border-yellow-400/20 px-2 py-1 rounded text-[10px] uppercase font-bold"
                                >Pending Claim</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showConfirmModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        >
            <div
                class="bg-[#162236] border border-brand-border-black w-full max-w-md rounded-2xl p-6 shadow-2xl animate-in fade-in zoom-in duration-200"
            >
                <div class="text-center space-y-4">
                    <div
                        class="bg-blue-500/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto border border-blue-500/20"
                    >
                        <i
                            class="fa-solid fa-credit-card text-blue-400 text-2xl"
                        ></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">
                            Confirm Payment
                        </h3>
                        <p class="text-brand-gray mt-2">
                            Proceed to pay
                            <span class="text-white font-bold"
                                >₱{{ formatCurrency(team.total_payment) }}</span
                            >?
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 pt-2">
                        <button
                            @click="processPayment"
                            :disabled="isProcessing"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition disabled:opacity-50"
                        >
                            <span v-if="isProcessing"
                                ><i
                                    class="fa-solid fa-spinner animate-spin mr-2"
                                ></i
                                >Loading...</span
                            >
                            <span v-else>Yes, Proceed to Payment</span>
                        </button>
                        <button
                            @click="showConfirmModal = false"
                            :disabled="isProcessing"
                            class="w-full bg-transparent hover:bg-white/5 text-brand-gray font-medium py-3 rounded-xl transition"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="currentModalMember"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/95 p-2 sm:p-4 overflow-y-auto"
            @click="closeQRModal"
        >
            <div
                class="relative max-w-sm w-full bg-[#162236] p-5 sm:p-6 rounded-2xl border border-brand-border-black flex flex-col items-center my-auto"
                @click.stop
            >
                <div class="text-center mb-3 sm:mb-4">
                    <h2
                        class="text-lg sm:text-xl font-bold text-white truncate max-w-[250px]"
                    >
                        {{ currentModalMember.full_name }}
                    </h2>
                    <p class="text-blue-400 text-xs sm:text-sm">
                        @{{ currentModalMember.username }}
                    </p>
                </div>

                <div
                    class="bg-white p-3 sm:p-4 rounded-xl shadow-2xl mb-4 sm:mb-6 mx-auto"
                >
                    <img
                        :src="'/qr_image/' + currentModalMember.qrcode_img"
                        class="w-48 h-48 xs:w-56 xs:h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 object-contain"
                    />
                </div>

                <div class="flex items-center justify-between w-full gap-2">
                    <button
                        @click="prevQR"
                        class="bg-white/10 hover:bg-white/20 text-white p-2 sm:p-3 rounded-full transition active:scale-95"
                    >
                        <i
                            class="fa-solid fa-chevron-left text-lg sm:text-xl"
                        ></i>
                    </button>

                    <button
                        @click="closeQRModal"
                        class="flex-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 py-2 rounded-xl font-bold transition text-sm sm:text-base"
                    >
                        Close
                    </button>

                    <button
                        @click="nextQR"
                        class="bg-white/10 hover:bg-white/20 text-white p-2 sm:p-3 rounded-full transition active:scale-95"
                    >
                        <i
                            class="fa-solid fa-chevron-right text-lg sm:text-xl"
                        ></i>
                    </button>
                </div>

                <p class="text-brand-gray text-[10px] sm:text-xs mt-3 sm:mt-4">
                    Member {{ selectedIndex + 1 }} of
                    {{ team.detail_user.length }}
                </p>
            </div>
        </div>
    </div>
</template>
