<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";

import { toast } from "vue-sonner";

const props = defineProps({
    admin: Object,
});

const showProfileConfirm = ref(false);
const showPasswordConfirm = ref(false);

// Profile form
const profileForm = useForm({
    full_name: props.admin.full_name,
    email: props.admin.email,
    contact_number: props.admin.contact_number,
});

// Password form
const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

// Submit profile update
const submitProfileUpdate = () => {
    showProfileConfirm.value = false;
    profileForm.patch("/admin/profile", { preserveScroll: true });
};

// Submit password update
const submitPasswordUpdate = () => {
    showPasswordConfirm.value = false;
    passwordForm.put("/admin/profile/password", {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            toast.success("sucess");
        },
    });
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <h2 class="text-2xl font-bold text-white">Account Settings</h2>

        <!-- PROFILE -->
        <div
            class="bg-brand-dark-black border border-brand-border-black rounded-2xl p-6 shadow-2xl"
        >
            <h3 class="text-xl font-semibold text-white mb-4">
                Profile Information
            </h3>

            <form @submit.prevent="showProfileConfirm = true" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >Full Name</label
                        >
                        <input
                            v-model="profileForm.full_name"
                            type="text"
                            class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 text-white outline-none"
                        />
                        <p
                            v-if="profileForm.errors.full_name"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ profileForm.errors.full_name }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >Email Address</label
                        >
                        <input
                            v-model="profileForm.email"
                            type="email"
                            class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 text-white outline-none"
                        />
                        <p
                            v-if="profileForm.errors.email"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ profileForm.errors.email }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-400 block mb-1"
                            >Contact Number</label
                        >
                        <input
                            v-model="profileForm.contact_number"
                            type="text"
                            class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 text-white outline-none"
                        />
                        <p
                            v-if="profileForm.errors.contact_number"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ profileForm.errors.contact_number }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button
                        type="submit"
                        :disabled="profileForm.processing"
                        class="bg-brand-blue hover:opacity-90 text-white font-bold py-2 px-6 rounded-lg transition"
                    >
                        Save Changes
                    </button>
                    <span
                        v-if="profileForm.recentlySuccessful"
                        class="text-green-500 text-sm"
                        >✓ Saved Successfully</span
                    >
                </div>
            </form>
        </div>

        <!-- PASSWORD -->
        <div
            class="bg-brand-dark-black border border-brand-border-black rounded-2xl p-6 shadow-2xl"
        >
            <h3 class="text-xl font-semibold text-white mb-4">Security</h3>

            <form
                @submit.prevent="showPasswordConfirm = true"
                class="space-y-5"
            >
                <div class="max-w-md space-y-4">
                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >Current Password</label
                        >
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 text-white outline-none"
                        />
                        <p
                            v-if="passwordForm.errors.current_password"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >New Password</label
                        >
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 text-white outline-none"
                        />
                        <p
                            v-if="passwordForm.errors.password"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >Confirm New Password</label
                        >
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 text-white outline-none"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="passwordForm.processing"
                    class="bg-brand-blue hover:opacity-90 text-white font-bold py-2 px-6 rounded-lg transition"
                >
                    Update Password
                </button>
                <span
                    v-if="passwordForm.recentlySuccessful"
                    class="text-green-500 text-sm ml-4"
                    >✓ Password Updated</span
                >
            </form>
        </div>

        <!-- PROFILE CONFIRM -->
        <AlertDialog
            :open="showProfileConfirm"
            @update:open="showProfileConfirm = $event"
        >
            <AlertDialogContent
                class="bg-brand-dark-black border border-brand-border-black text-white"
            >
                <AlertDialogHeader>
                    <AlertDialogTitle>Confirm Profile Update?</AlertDialogTitle>
                    <AlertDialogDescription class="text-gray-400">
                        Please confirm that you want to save the changes to your
                        profile.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        type="button"
                        class="bg-gray-800 text-white border-none"
                        >Cancel</AlertDialogCancel
                    >
                    <AlertDialogAction
                        @click="submitProfileUpdate"
                        :disabled="profileForm.processing"
                        class="bg-brand-blue"
                    >
                        Save Changes
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

        <!-- PASSWORD CONFIRM -->
        <AlertDialog
            :open="showPasswordConfirm"
            @update:open="showPasswordConfirm = $event"
        >
            <AlertDialogContent
                class="bg-brand-dark-black border border-brand-border-black text-white"
            >
                <AlertDialogHeader>
                    <AlertDialogTitle
                        >Confirm Password Change?</AlertDialogTitle
                    >
                    <AlertDialogDescription class="text-gray-400">
                        Are you sure you want to change your password? You will
                        need to use the new one for your next login.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        type="button"
                        class="bg-gray-800 text-white border-none"
                        >Cancel</AlertDialogCancel
                    >
                    <AlertDialogAction
                        @click="submitPasswordUpdate"
                        :disabled="passwordForm.processing"
                        class="bg-red-600 hover:bg-red-700"
                    >
                        Change Password
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
