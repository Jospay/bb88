<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
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
import Toast from "@/components/Toast.vue";
import { Eye, EyeOff, Loader2 } from "lucide-vue-next"; // Added Loader2 for loading icon

const props = defineProps({
    player: Object,
});

const page = usePage();

// --- CUSTOM TOAST LOGIC ---
const notifications = ref([]);

const addNotification = (message, type = "success") => {
    const id = Date.now();
    notifications.value.push({ id, message, type });
};

const removeNotification = (id) => {
    notifications.value = notifications.filter((n) => n.id !== id);
};

// Automatically trigger toast if server sends success/error flash
watch(
    () => page.props.flash.success,
    (msg) => {
        if (msg) addNotification(msg, "success");
    },
    { immediate: true },
);

watch(
    () => page.props.flash.error,
    (msg) => {
        if (msg) addNotification(msg, "error");
    },
);
// --------------------------

const showProfileConfirm = ref(false);
const showPasswordConfirm = ref(false);

const showCurrentPw = ref(false);
const showNewPw = ref(false);
const showConfirmPw = ref(false);
const isNewPasswordFocused = ref(false);

const profileForm = useForm({
    full_name: props.player.full_name,
    email: props.player.email,
    contact_number: props.player.contact_number,
});

const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const passwordRequirements = computed(() => [
    {
        label: "At least 8 characters long",
        met: passwordForm.password.length >= 8,
    },
    {
        label: "At least one uppercase letter",
        met: /[A-Z]/.test(passwordForm.password),
    },
    {
        label: "At least one lowercase letter",
        met: /[a-z]/.test(passwordForm.password),
    },
    { label: "At least one number", met: /[0-9]/.test(passwordForm.password) },
]);

const allRequirementsMet = computed(() => {
    return passwordRequirements.value.every((req) => req.met);
});

const submitProfileUpdate = () => {
    showProfileConfirm.value = false;
    profileForm.patch("/player/profile", {
        preserveScroll: true,
        onSuccess: () => addNotification("Profile updated successfully!"),
    });
};

const submitPasswordUpdate = () => {
    showPasswordConfirm.value = false;
    passwordForm.put("/player/profile/password", {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            addNotification("Password changed successfully!");
        },
        onError: () =>
            addNotification("Please check the password requirements.", "error"),
    });
};
</script>

<template>
    <div
        aria-live="assertive"
        class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:p-6 z-999"
    >
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <Toast
                v-for="n in notifications"
                :key="n.id"
                :message="n.message"
                :type="n.type"
                @close="removeNotification(n.id)"
            />
        </div>
    </div>

    <div
        class="max-w-3xl mx-auto space-y-6 grid place-items-center h-full pb-20"
    >
        <div
            class="bg-brand-dark-black border border-brand-border-black rounded-2xl p-6 w-full shadow-2xl"
        >
            <h3 class="text-xl font-semibold text-white mb-4">Security</h3>

            <form
                @submit.prevent="showPasswordConfirm = true"
                class="space-y-5"
            >
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >Current Password</label
                        >
                        <div class="relative">
                            <input
                                v-model="passwordForm.current_password"
                                :type="showCurrentPw ? 'text' : 'password'"
                                class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 pr-10 text-white outline-none focus:border-brand-blue"
                            />
                            <button
                                type="button"
                                @click="showCurrentPw = !showCurrentPw"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                            >
                                <component
                                    :is="showCurrentPw ? EyeOff : Eye"
                                    class="w-5 h-5"
                                />
                            </button>
                        </div>
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
                        <div class="relative">
                            <input
                                v-model="passwordForm.password"
                                :type="showNewPw ? 'text' : 'password'"
                                @focus="isNewPasswordFocused = true"
                                @blur="isNewPasswordFocused = false"
                                class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 pr-10 text-white outline-none focus:border-brand-blue"
                            />
                            <button
                                type="button"
                                @click="showNewPw = !showNewPw"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                            >
                                <component
                                    :is="showNewPw ? EyeOff : Eye"
                                    class="w-5 h-5"
                                />
                            </button>
                        </div>

                        <div
                            v-if="isNewPasswordFocused && !allRequirementsMet"
                            class="mt-3 p-3 bg-black/30 rounded-lg border border-brand-border-black space-y-1"
                        >
                            <div
                                v-for="req in passwordRequirements"
                                :key="req.label"
                                class="flex items-center text-xs transition-colors duration-300"
                                :class="
                                    req.met ? 'text-green-400' : 'text-gray-500'
                                "
                            >
                                <span class="mr-2">{{
                                    req.met ? "✓" : "○"
                                }}</span>
                                {{ req.label }}
                            </div>
                        </div>
                        <p
                            v-if="passwordForm.errors.password"
                            class="text-red-500 text-xs mt-2"
                        >
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-400 block mb-1"
                            >Confirm New Password</label
                        >
                        <div class="relative">
                            <input
                                v-model="passwordForm.password_confirmation"
                                :type="showConfirmPw ? 'text' : 'password'"
                                class="w-full bg-brand-light-black border border-brand-border-black rounded-lg p-2.5 pr-10 text-white outline-none focus:border-brand-blue"
                            />
                            <button
                                type="button"
                                @click="showConfirmPw = !showConfirmPw"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                            >
                                <component
                                    :is="showConfirmPw ? EyeOff : Eye"
                                    class="w-5 h-5"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="passwordForm.processing"
                    class="bg-brand-blue hover:opacity-90 disabled:opacity-50 text-white font-bold py-2 px-6 rounded-lg transition flex items-center gap-2"
                >
                    <Loader2
                        v-if="passwordForm.processing"
                        class="w-4 h-4 animate-spin"
                    />
                    {{
                        passwordForm.processing
                            ? "Updating..."
                            : "Update Password"
                    }}
                </button>
            </form>
        </div>

        <AlertDialog
            :open="showProfileConfirm"
            @update:open="showProfileConfirm = $event"
        >
            <AlertDialogContent
                class="bg-brand-dark-black border border-brand-border-black text-white"
            >
                <AlertDialogHeader>
                    <AlertDialogTitle>Confirm Profile Update?</AlertDialogTitle>
                    <AlertDialogDescription class="text-gray-400"
                        >Please confirm that you want to save the changes to
                        your profile.</AlertDialogDescription
                    >
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="bg-gray-800 text-white border-none hover:bg-gray-700"
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
                    <AlertDialogDescription class="text-gray-400"
                        >Are you sure you want to change your password? You will
                        need to use the new one for your next
                        login.</AlertDialogDescription
                    >
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="bg-gray-800 text-white border-none hover:bg-gray-700"
                        >Cancel</AlertDialogCancel
                    >
                    <AlertDialogAction
                        @click="submitPasswordUpdate"
                        :disabled="passwordForm.processing"
                        class="bg-red-600 hover:bg-red-700 text-white"
                    >
                        Change Password
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
