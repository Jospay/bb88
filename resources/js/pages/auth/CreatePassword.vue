<script setup>
import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";

const props = defineProps({ token: String });
const showSuccessModal = ref(false);

const form = useForm({
    token: props.token,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post("/create-password", {
        onSuccess: () => {
            showSuccessModal.value = true;
        },
    });
};

const goToDashboard = () => {
    showSuccessModal.value = false;
    router.visit("/player");
};
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center bg-brand-light-black p-4 relative"
    >
        <div
            class="w-full max-w-md bg-brand-dark-black rounded-2xl shadow-xl p-8 border border-gray-800"
        >
            <h2 class="text-2xl font-bold text-brand-blue text-center mb-6">
                Create New Password
            </h2>

            <form @submit.prevent="submit" class="space-y-4">
                <input type="hidden" v-model="form.token" />

                <div>
                    <label class="block text-sm font-medium text-white mb-1"
                        >New Password</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-700 bg-transparent text-white px-4 py-3 focus:ring-2 focus:ring-brand-blue outline-none transition"
                        :class="{ 'border-red-500': form.errors.password }"
                    />
                    <div
                        v-if="form.errors.password"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-1"
                        >Confirm Password</label
                    >
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-700 bg-transparent text-white px-4 py-3 focus:ring-2 focus:ring-brand-blue outline-none transition"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-brand-blue text-white font-bold py-3 rounded-xl hover:opacity-90 transition disabled:opacity-50"
                >
                    {{ form.processing ? "Updating..." : "Update Password" }}
                </button>
            </form>
        </div>

        <div
            v-if="showSuccessModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        >
            <div
                class="bg-brand-dark-black w-full max-w-sm rounded-2xl p-8 border border-brand-blue/30 shadow-2xl text-center transform transition-all scale-100"
            >
                <div
                    class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6"
                >
                    <i class="fas fa-check text-green-500 text-3xl"></i>
                </div>

                <h3 class="text-2xl font-bold text-white mb-2">
                    Password Updated
                </h3>
                <p class="text-gray-400 mb-8 text-sm">
                    Your account security has been updated successfully. You can
                    now access your dashboard.
                </p>

                <button
                    @click="goToDashboard"
                    class="w-full bg-brand-blue text-white font-bold py-3 rounded-xl hover:bg-blue-600 transition shadow-lg shadow-brand-blue/20"
                >
                    Continue to Dashboard
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Optional: Simple fade in for modal */
.fixed {
    animation: fadeIn 0.2s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
