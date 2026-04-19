<script setup>
import { useForm, Link } from "@inertiajs/vue3"; // Added Link component
import { ref } from "vue";

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post("/login", {
        onFinish: () => {
            if (form.hasErrors) {
                form.reset("password");
            }
        },
    });
};
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center bg-brand-light-black"
    >
        <div
            class="w-full max-w-md bg-brand-dark-black rounded-2xl shadow-xl p-8"
        >
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-brand-blue">Player Panel</h1>
                <p class="text-sm text-gray-500 mt-1">Sign in to continue</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-white mb-1"
                        >Email address</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="player@email.com"
                        class="w-full rounded-xl border text-white border-gray-300 bg-transparent px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue"
                    />
                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-white"
                            >Password</label
                        >
                    </div>
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="w-full rounded-xl border text-white border-gray-300 bg-transparent px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue pr-10"
                        />

                        <button
                            @click="showPassword = !showPassword"
                            type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-blue transition-colors"
                        >
                            <i
                                :class="[
                                    'fas',
                                    showPassword ? 'fa-eye-slash' : 'fa-eye',
                                ]"
                            ></i>
                        </button>
                    </div>

                    <div
                        v-if="form.errors.password"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.password }}
                    </div>

                    <div class="text-end pt-3">
                        <Link
                            href="/forgot-password"
                            class="text-xs text-brand-blue hover:text-blue-900 underline"
                        >
                            Forgot password?
                        </Link>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-brand-blue hover:opacity-90 text-white font-semibold py-3 rounded-xl transition disabled:opacity-50"
                >
                    {{ form.processing ? "Authenticating..." : "Sign In" }}
                </button>
            </form>

            <div class="mt-6 text-center border-t border-gray-800 pt-6">
                <Link
                    href="/"
                    class="text-sm text-gray-400 hover:text-white transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-arrow-left text-xs"></i>
                    Return to Home
                </Link>
            </div>
        </div>
    </div>
</template>
