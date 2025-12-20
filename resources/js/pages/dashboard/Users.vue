<script setup>
import { ref, h } from "vue";
import { Head } from "@inertiajs/vue3";
import {
    useVueTable,
    getCoreRowModel,
    getPaginationRowModel,
    getFilteredRowModel,
    FlexRender,
} from "@tanstack/vue-table";

// shadcn UI components
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const props = defineProps({
    users: Array,
});

const columns = [
    {
        accessorKey: "full_name",
        header: "Name",
        cell: ({ row }) =>
            h(
                "span",
                { class: "text-white font-medium" },
                row.original.full_name
            ),
    },
    {
        accessorKey: "email",
        header: "Email",
        cell: ({ row }) =>
            h("span", { class: "text-white font-medium" }, row.original.email),
    },
    // NEW COLUMN: Transaction Status (from the relationship)
    {
        accessorKey: "user.transaction_status",
        header: "Payment Status",
        cell: ({ row }) => {
            const status = row.original.user?.transaction_status;
            const variants = {
                paid: "bg-green-500",
                pending_payment: "bg-orange-500",
                pending_registration: "bg-blue-500",
                failed: "bg-red-500",
            };
            return h(
                Badge,
                {
                    class: `${
                        variants[status] || "bg-gray-500"
                    } text-white uppercase text-[10px]`,
                },
                () => status?.replace("_", " ") || "Unknown"
            );
        },
        filterFn: (row, columnId, filterValue) => {
            if (!filterValue) return true;
            return row.original.user?.transaction_status === filterValue;
        },
    },
    {
        accessorKey: "account_type",
        header: "Type",
        cell: ({ row }) =>
            h(
                Badge,
                {
                    variant: "outline",
                    class: "capitalize border-brand-blue text-brand-blue",
                },
                () => row.original.account_type
            ),
    },
    {
        accessorKey: "verification_account",
        header: "Verified",
        cell: ({ row }) => {
            const isVerified =
                parseInt(row.original.verification_account) === 1;
            return h(
                Badge,
                {
                    class: isVerified
                        ? "bg-brand-green text-white"
                        : "bg-brand-red text-white",
                },
                () => (isVerified ? "Verified" : "Unverified")
            );
        },
        filterFn: (row, columnId, filterValue) => {
            if (filterValue === "all") return true;
            return String(row.getValue(columnId)) === String(filterValue);
        },
    },
    {
        accessorKey: "status",
        header: "Claiming Shirt",
        cell: ({ row }) => {
            const isClaimed = row.original.status === "claimed";
            return h(
                Badge,
                {
                    class: isClaimed
                        ? "bg-brand-lightgreen text-white"
                        : "animate-pulse border-brand-yellow text-brand-yellow bg-brand-yellow/10",
                    variant: isClaimed ? "default" : "outline",
                },
                () => row.original.status
            );
        },
    },
];

const columnFilters = ref([]);

const table = useVueTable({
    get data() {
        return props.users;
    },
    columns,
    state: {
        get columnFilters() {
            return columnFilters.value;
        },
    },
    onColumnFiltersChange: (updater) => {
        columnFilters.value =
            typeof updater === "function"
                ? updater(columnFilters.value)
                : updater;
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
});
</script>

<template>
    <Head title="User Management" />
    <div class="space-y-6 text-white">
        <div class="flex flex-col gap-1">
            <h1 class="text-3xl font-bold tracking-tight text-brand-blue">
                User Management
            </h1>
            <p class="text-brand-gray text-sm">
                Manage tournament players, payments, and shirt collection.
            </p>
        </div>

        <div
            class="bg-brand-dark-black rounded-2xl p-6 border border-brand-border-black shadow-2xl"
        >
            <div class="w-full space-y-4">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4"
                >
                    <Input
                        placeholder="Search by email..."
                        :model-value="
                            table.getColumn('email')?.getFilterValue()
                        "
                        @update:model-value="
                            table.getColumn('email')?.setFilterValue($event)
                        "
                        class="bg-brand-light-black border-brand-border-black text-white"
                    />

                    <select
                        class="bg-brand-light-black border-brand-border-black text-white rounded-md px-3 py-2 text-sm outline-none"
                        :value="
                            table
                                .getColumn('user_transaction_status')
                                ?.getFilterValue() ?? ''
                        "
                        @change="
                            table
                                .getColumn('user_transaction_status')
                                ?.setFilterValue(
                                    $event.target.value || undefined
                                )
                        "
                    >
                        <option value="">All Payment Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending_payment">Pending Payment</option>
                        <option value="pending_registration">
                            Pending Reg
                        </option>
                        <option value="failed">Failed</option>
                    </select>

                    <select
                        class="bg-brand-light-black border-brand-border-black text-white rounded-md px-3 py-2 text-sm outline-none"
                        :value="
                            table.getColumn('account_type')?.getFilterValue() ??
                            ''
                        "
                        @change="
                            table
                                .getColumn('account_type')
                                ?.setFilterValue(
                                    $event.target.value || undefined
                                )
                        "
                    >
                        <option value="">All Account Types</option>
                        <option value="Player">Player</option>
                        <option value="Shirt">Shirt</option>
                    </select>

                    <select
                        class="bg-brand-light-black border-brand-border-black text-white rounded-md px-3 py-2 text-sm outline-none"
                        :value="
                            table
                                .getColumn('verification_account')
                                ?.getFilterValue() ?? ''
                        "
                        @change="
                            table
                                .getColumn('verification_account')
                                ?.setFilterValue(
                                    $event.target.value || undefined
                                )
                        "
                    >
                        <option value="">All Verification</option>
                        <option value="1">Verified</option>
                        <option value="0">Unverified</option>
                    </select>

                    <select
                        class="bg-brand-light-black border-brand-border-black text-white rounded-md px-3 py-2 text-sm outline-none"
                        :value="
                            table.getColumn('status')?.getFilterValue() ?? ''
                        "
                        @change="
                            table
                                .getColumn('status')
                                ?.setFilterValue(
                                    $event.target.value || undefined
                                )
                        "
                    >
                        <option value="">All Shirt Status</option>
                        <option value="claimed">Claimed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div
                    class="rounded-xl border border-brand-border-black overflow-hidden bg-brand-dark-black"
                >
                    <Table>
                        <TableHeader class="bg-brand-light-black">
                            <TableRow
                                v-for="headerGroup in table.getHeaderGroups()"
                                :key="headerGroup.id"
                            >
                                <TableHead
                                    v-for="header in headerGroup.headers"
                                    :key="header.id"
                                    class="text-white font-bold py-4 uppercase text-xs"
                                >
                                    <FlexRender
                                        v-if="!header.isPlaceholder"
                                        :render="header.column.columnDef.header"
                                        :props="header.getContext()"
                                    />
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="row in table.getRowModel().rows"
                                :key="row.id"
                                class="border-b border-brand-border-black hover:bg-brand-blue/5"
                            >
                                <TableCell
                                    v-for="cell in row.getVisibleCells()"
                                    :key="cell.id"
                                    class="py-4 px-4"
                                >
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()"
                                    />
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </div>
</template>
