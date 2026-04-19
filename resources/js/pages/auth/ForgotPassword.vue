<script setup>
import { useForm, Link } from "@inertiajs/vue3";

const form = useForm({
    email: "",
});

const submit = () => {
    form.post("/forgot-password");
};
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center bg-brand-light-black p-4"
    >
        <div
            class="w-full max-w-md bg-brand-dark-black rounded-2xl shadow-xl p-8"
        >
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-brand-blue">
                    Reset Password
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Enter your email to receive a verification link.
                </p>
            </div>

            <div
                v-if="$page.props.flash.status"
                class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-500 text-sm text-center"
            >
                <i class="fas fa-check-circle mr-2"></i>
                {{ $page.props.flash.status }}
            </div>

            <form v-else @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-white mb-1"
                        >Email Address</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="player@email.com"
                        class="w-full rounded-xl border border-gray-300 bg-transparent text-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue"
                        :class="{ 'border-red-500': form.errors.email }"
                    />
                    <div
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-brand-blue hover:opacity-90 text-white font-semibold py-3 rounded-xl transition disabled:opacity-50"
                >
                    {{ form.processing ? "Sending..." : "Send Reset Link" }}
                </button>
            </form>

            <div class="mt-6 text-center border-t border-gray-800 pt-6">
                <Link
                    href="/login"
                    class="text-sm text-gray-400 hover:text-white transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-arrow-left text-xs"></i> Back to Sign In
                </Link>
            </div>
        </div>
    </div>
</template>
