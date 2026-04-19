<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    team: Object,
});

const isProcessing = ref(false);
const showConfirmModal = ref(false);

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

// Function to trigger the modal
const confirmPayment = () => {
    // PREVENT MODAL FROM OPENING IF ALREADY PROCESSING
    if (isProcessing.value) return;
    showConfirmModal.value = true;
};

// Function that actually executes the payment
const processPayment = () => {
    // Immediately close modal and lock the process
    showConfirmModal.value = false;
    isProcessing.value = true;

    router.post(
        "/player/payment",
        {},
        {
            onStart: () => {
                isProcessing.value = true;
            },
            onFinish: () => {
                // Keep it true during redirect to avoid "flicker"
                // It will reset naturally if the page reloads/redirects
            },
            onError: () => {
                // Only reset if there's a literal error so they can try again
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
                    class="hidden md:grid md:grid-cols-4 text-brand-gray text-sm uppercase tracking-wider font-medium pb-4 px-2"
                >
                    <div>Name / Username</div>
                    <div class="text-center">Shirt Size</div>
                    <div class="text-center">Type</div>
                    <div class="text-right">Claim Shirt Status</div>
                </div>

                <div
                    class="space-y-4 md:space-y-0 divide-y md:divide-y divide-brand-border-black"
                >
                    <div
                        v-for="member in team.detail_user"
                        :key="member.id"
                        class="flex flex-col md:grid md:grid-cols-4 py-4 gap-3 md:gap-0 hover:bg-white/5 transition-colors px-2 rounded-xl md:rounded-none"
                    >
                        <div class="flex justify-between md:block">
                            <span
                                class="md:hidden text-brand-gray text-xs uppercase font-bold"
                                >Member</span
                            >
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

                <p
                    v-if="!team.detail_user || team.detail_user.length === 0"
                    class="text-center text-brand-gray py-8 italic"
                >
                    No team members found.
                </p>
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
                            You are about to be redirected to the payment
                            gateway to pay
                            <span class="text-white font-bold"
                                >₱{{ formatCurrency(team.total_payment) }}</span
                            >. Do you wish to proceed?
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 pt-2">
                        <button
                            @click="processPayment"
                            :disabled="isProcessing"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-600/20 disabled:opacity-50"
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
                            class="w-full bg-transparent hover:bg-white/5 text-brand-gray font-medium py-3 rounded-xl transition disabled:opacity-30"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
