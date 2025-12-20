<script setup>
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";

// UI Component Imports
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
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const props = defineProps({
    allocation: Array,
});

const showModal = ref(false);
const showDeleteAlert = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const itemToDelete = ref(null);

const form = useForm({
    name: "",
    value: 0,
});

/**
 * Middleman for the name input.
 * GET: Replaces underscores with spaces for the user to see.
 * SET: Replaces spaces with underscores for the form/database.
 */
const displayName = computed({
    get: () => form.name.replace(/_/g, " "),
    set: (val) => {
        form.name = val.replace(/\s+/g, "_");
    },
});

// Helper to handle the "route is not defined" error globally
const ziggyRoute = (name, params) => {
    try {
        return route(name, params);
    } catch (e) {
        if (name.includes("update") || name.includes("destroy")) {
            return `/admin/allocation/${params.allocation}`;
        }
        return "/admin/allocation";
    }
};

// Global total of all percentages
const totalPercentage = computed(() => {
    return props.allocation.reduce(
        (sum, item) => sum + parseFloat(item.value || 0),
        0
    );
});

// Remaining capacity calculation
const remainingPercentage = computed(() => {
    let currentSum = totalPercentage.value;
    if (isEditing.value) {
        const item = props.allocation.find((a) => a.id === editingId.value);
        if (item) currentSum -= parseFloat(item.value || 0);
    }
    return parseFloat(Math.max(0, 100 - currentSum).toFixed(2));
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    editingId.value = item.id;
    form.name = item.name;
    form.value = parseFloat(item.value);
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    const url = isEditing.value
        ? ziggyRoute("admin.allocation.update", { allocation: editingId.value })
        : ziggyRoute("admin.allocation.store");

    if (isEditing.value) {
        form.put(url, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(url, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

const confirmDelete = (id) => {
    itemToDelete.value = id;
    showDeleteAlert.value = true;
};

const executeDelete = () => {
    router.delete(
        ziggyRoute("admin.allocation.destroy", {
            allocation: itemToDelete.value,
        }),
        {
            preserveScroll: true,
            onSuccess: () => (showDeleteAlert.value = false),
        }
    );
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Percentage Allocations
                </h1>
                <p class="text-slate-400 text-sm">
                    Total:
                    <span
                        :class="
                            totalPercentage >= 100
                                ? 'text-red-500'
                                : 'text-blue-400'
                        "
                        class="font-bold"
                    >
                        {{ totalPercentage }}%
                    </span>
                    / 100%
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <Card
                v-for="item in allocation"
                :key="item.id"
                class="bg-[#162236] border-slate-800 text-white min-h-[160px] flex flex-col justify-between"
            >
                <CardHeader
                    class="flex flex-row items-center justify-between space-y-0 pb-2"
                >
                    <CardTitle
                        class="text-xs font-bold uppercase text-slate-400"
                    >
                        {{ item.name.replace(/_/g, " ") }}
                    </CardTitle>
                    <span class="text-blue-400 font-bold">%</span>
                </CardHeader>
                <CardContent>
                    <div class="my-4">
                        <h3 class="text-3xl font-black text-white">
                            {{ parseFloat(item.value) }}%
                        </h3>
                    </div>

                    <div
                        class="flex justify-end space-x-3 pt-4 border-t border-slate-800"
                    >
                        <button
                            @click="openEditModal(item)"
                            class="text-blue-400 text-xs font-bold uppercase hover:underline"
                        >
                            Edit
                        </button>
                        <button
                            @click="confirmDelete(item.id)"
                            class="text-red-400 text-xs font-bold uppercase hover:underline"
                        >
                            Delete
                        </button>
                    </div>
                </CardContent>
            </Card>

            <button
                v-if="totalPercentage < 100"
                @click="openCreateModal"
                class="border-2 border-dashed border-slate-800 rounded-xl p-6 flex flex-col items-center justify-center min-h-[160px] hover:bg-white/5 hover:border-blue-500/50 transition-all group"
            >
                <i
                    class="fa-solid fa-plus text-blue-400 mb-2 group-hover:scale-110 transition-transform"
                ></i>
                <span
                    class="text-slate-400 text-sm font-medium group-hover:text-blue-400"
                    >Add New Split</span
                >
            </button>
        </div>

        <Dialog :open="showModal" @update:open="showModal = $event">
            <DialogContent
                class="bg-[#162236] border-slate-800 text-white sm:max-w-[425px]"
            >
                <DialogHeader>
                    <DialogTitle
                        >{{
                            isEditing ? "Update" : "Create"
                        }}
                        Allocation</DialogTitle
                    >
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label
                            for="name"
                            class="text-xs uppercase font-bold text-slate-400"
                            >Label Name</Label
                        >
                        <Input
                            id="name"
                            v-model="displayName"
                            class="bg-[#0B1120] border-slate-700"
                            placeholder="e.g. System Fee"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label
                            for="value"
                            class="text-xs uppercase font-bold text-slate-400"
                        >
                            Percentage (Max Available:
                            {{ remainingPercentage }}%)
                        </Label>
                        <Input
                            id="value"
                            v-model="form.value"
                            type="number"
                            step="0.01"
                            :max="remainingPercentage"
                            class="bg-[#0B1120] border-slate-700"
                        />
                        <p
                            v-if="form.errors.value"
                            class="text-red-400 text-xs"
                        >
                            {{ form.errors.value }}
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="ghost" @click="showModal = false"
                        >Cancel</Button
                    >
                    <Button
                        @click="submit"
                        :disabled="form.processing"
                        class="bg-blue-600"
                    >
                        {{ isEditing ? "Update" : "Save" }} Allocation
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <AlertDialog
            :open="showDeleteAlert"
            @update:open="showDeleteAlert = $event"
        >
            <AlertDialogContent
                class="bg-[#162236] border-slate-800 text-white"
            >
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                    <AlertDialogDescription class="text-slate-400">
                        This action will remove this allocation split.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="bg-transparent border-slate-700 text-white"
                        >Cancel</AlertDialogCancel
                    >
                    <AlertDialogAction
                        @click="executeDelete"
                        class="bg-red-600 text-white hover:bg-red-700"
                    >
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
