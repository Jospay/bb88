<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post("/admin/login", {
        onFinish: () => form.reset("password"),
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
                <h1 class="text-2xl font-bold text-brand-blue">Admin Panel</h1>
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
                        placeholder="admin@email.com"
                        class="w-full rounded-xl border text-white border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue"
                    />
                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-1"
                        >Password</label
                    >
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="••••••••"
                            class="w-full rounded-xl border text-white border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue pr-10"
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
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-brand-blue hover:opacity-90 text-white font-semibold py-3 rounded-xl transition"
                >
                    {{ form.processing ? "Authenticating..." : "Sign In" }}
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-8">
                © 2025 Admin System. All rights reserved.
            </p>
        </div>
    </div>
</template>
