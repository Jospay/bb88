<script setup>
import { ref, onMounted } from "vue";
import { CheckCircle, AlertCircle, X, Info } from "lucide-vue-next";

const props = defineProps({
    message: String,
    type: { type: String, default: "success" }, // success, error, info
    duration: { type: Number, default: 3000 },
});

const emit = defineEmits(["close"]);
const visible = ref(true);

const close = () => {
    visible.value = false;
    // Delay the emit to allow the Transition leave animation to finish
    setTimeout(() => emit("close"), 300);
};

onMounted(() => {
    setTimeout(close, props.duration);
});
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible"
            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl bg-brand-dark-black border border-brand-border-black shadow-2xl ring-1 ring-white/5"
        >
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0">
                        <CheckCircle
                            v-if="type === 'success'"
                            class="h-5 w-5 text-green-500"
                        />
                        <AlertCircle
                            v-else-if="type === 'error'"
                            class="h-5 w-5 text-red-500"
                        />
                        <Info v-else class="h-5 w-5 text-brand-blue" />
                    </div>

                    <div class="flex-1 pt-0.5">
                        <p class="text-sm font-medium text-white leading-5">
                            {{ message }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        <button
                            @click="close"
                            class="inline-flex rounded-md text-gray-500 hover:text-white transition-colors focus:outline-none"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="h-1 bg-brand-light-black w-full overflow-hidden">
                <div
                    class="h-full bg-brand-blue"
                    :style="{
                        animation: `progress ${duration}ms linear forwards`,
                    }"
                ></div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes progress {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>
