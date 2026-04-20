<script setup>
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";

const props = defineProps({ token: String });
const showSuccessModal = ref(false);

// Separate toggles for each field
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    password: "",
    password_confirmation: "",
});

// Password Validation Logic
const passwordRequirements = computed(() => [
    { label: "At least 8 characters long", met: form.password.length >= 8 },
    {
        label: "At least one uppercase letter",
        met: /[A-Z]/.test(form.password),
    },
    {
        label: "At least one lowercase letter",
        met: /[a-z]/.test(form.password),
    },
    { label: "At least one number", met: /[0-9]/.test(form.password) },
]);

const allRequirementsMet = computed(() =>
    passwordRequirements.value.every((req) => req.met),
);

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

                <div class="relative">
                    <label class="block text-sm font-medium text-white mb-1"
                        >New Password</label
                    >
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-gray-700 bg-transparent text-white pl-4 pr-12 py-3 focus:ring-2 focus:ring-brand-blue outline-none transition"
                            :class="{ 'border-red-500': form.errors.password }"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                        >
                            <i
                                :class="
                                    showPassword
                                        ? 'fas fa-eye-slash'
                                        : 'fas fa-eye'
                                "
                            ></i>
                        </button>
                    </div>

                    <div
                        v-if="!allRequirementsMet && form.password.length > 0"
                        class="mt-3 space-y-1"
                    >
                        <p
                            class="text-xs font-semibold text-gray-400 uppercase mb-2"
                        >
                            Password must include:
                        </p>
                        <div
                            v-for="(req, index) in passwordRequirements"
                            :key="index"
                            class="flex items-center text-xs transition-opacity duration-300"
                            :class="
                                req.met
                                    ? 'text-green-500 opacity-50'
                                    : 'text-red-400'
                            "
                        >
                            <i
                                :class="[
                                    'fas mr-2',
                                    req.met ? 'fa-check-circle' : 'fa-circle',
                                ]"
                            ></i>
                            {{ req.label }}
                        </div>
                    </div>

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
                    <div class="relative">
                        <input
                            v-model="form.password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-gray-700 bg-transparent text-white pl-4 pr-12 py-3 focus:ring-2 focus:ring-brand-blue outline-none transition"
                        />
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                        >
                            <i
                                :class="
                                    showConfirmPassword
                                        ? 'fas fa-eye-slash'
                                        : 'fas fa-eye'
                                "
                            ></i>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing || !allRequirementsMet"
                    class="w-full bg-brand-blue text-white font-bold py-3 rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed"
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
                    Your account security has been updated. You can now access
                    your dashboard.
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
