<script setup>
import { computed } from "vue";

const props = defineProps({
    stats: Object,
    latest_teams: Array,
});

// Helper for currency formatting
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-PH", {
        minimumFractionDigits: 2,
    }).format(value);
};
</script>

<template>
    <div class="text-white space-y-6">
        <div>
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <p class="text-brand-gray text-sm">
                Welcome back! Here’s a summary of your management data.
            </p>
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
                            Total Paid Players
                        </p>
                        <h3 class="text-3xl font-bold mt-1">
                            {{ stats.total_players }}
                        </h3>
                    </div>
                    <div class="bg-brand-dark-black p-3 rounded-xl">
                        <i class="fa-solid fa-users text-blue-400"></i>
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
                            Total Paid Shirts
                        </p>
                        <h3 class="text-3xl font-bold mt-1">
                            {{ stats.total_shirts }}
                        </h3>
                    </div>
                    <div class="bg-brand-dark-black p-3 rounded-xl">
                        <i class="fa-solid fa-shirt text-blue-400"></i>
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
                            Total Earnings
                        </p>
                        <h3 class="text-3xl font-bold mt-1">
                            ₱{{ stats.total_earnings }}
                        </h3>
                    </div>
                    <div class="bg-brand-dark-black p-3 rounded-xl">
                        <i class="fa-solid fa-coins text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="bg-[#162236] rounded-2xl p-8 border border-brand-border-black min-h-[300px]"
        >
            <h2
                class="text-lg font-semibold mb-4 border-b border-brand-border-black pb-2"
            >
                Recent Registered Teams (Latest 10)
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="text-brand-gray text-sm uppercase tracking-wider"
                        >
                            <th class="pb-4 font-medium">Team Name</th>
                            <th class="pb-4 font-medium">Members</th>
                            <th class="pb-4 font-medium">Amount</th>
                            <th class="pb-4 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border-black">
                        <tr
                            v-for="team in latest_teams"
                            :key="team.id"
                            class="text-sm hover:bg-white/5 transition-colors"
                        >
                            <td class="py-4 font-bold text-white">
                                {{ team.team_name || "N/A" }}
                            </td>
                            <td class="py-4 text-brand-gray">
                                {{ team.detail_user_count }} Members
                            </td>
                            <td class="py-4 text-white font-mono">
                                ₱{{ formatCurrency(team.total_payment) }}
                            </td>
                            <td class="py-4 text-right">
                                <span
                                    :class="{
                                        'text-green-400 bg-green-400/10 border-green-400/20':
                                            team.transaction_status === 'paid',
                                        'text-yellow-400 bg-yellow-400/10 border-yellow-400/20':
                                            team.transaction_status !== 'paid',
                                    }"
                                    class="px-2 py-1 rounded border text-[10px] uppercase font-bold"
                                >
                                    {{
                                        team.transaction_status?.replace(
                                            "_",
                                            " ",
                                        ) || "Unknown"
                                    }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p
                    v-if="latest_teams.length === 0"
                    class="text-center text-brand-gray py-8 italic"
                >
                    No teams registered yet.
                </p>
            </div>
        </div>
    </div>
</template>
