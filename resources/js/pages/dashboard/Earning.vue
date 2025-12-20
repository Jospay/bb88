<script setup>
import { h, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { useVueTable, getCoreRowModel, FlexRender } from "@tanstack/vue-table";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

const props = defineProps({
    totalCollected: Number,
    percentageTypes: Array,
    tableData: Array,
    currentFilter: String,
});

const handleFilterChange = (event) => {
    router.get(
        "/admin/earning",
        { filter: event.target.value },
        { preserveState: true, replace: true }
    );
};

const exportReport = (format) => {
    window.location.href = `/admin/earning/export/${format}?filter=${props.currentFilter}`;
};

const columns = computed(() => {
    const cols = [
        {
            accessorKey: "date",
            header: "Period",
            cell: ({ row }) =>
                h(
                    "span",
                    { class: "font-medium text-white" },
                    row.original.date
                ),
        },
        {
            accessorKey: "total_amount",
            header: "Total Collected",
            cell: ({ row }) =>
                h(
                    "span",
                    { class: "font-bold text-brand-blue" },
                    `₱${parseFloat(row.original.total_amount).toLocaleString()}`
                ),
        },
    ];

    props.percentageTypes.forEach((type) => {
        cols.push({
            accessorKey: type.name,
            header: type.name.replace("_", " "),
            cell: ({ row }) =>
                h(
                    "span",
                    { class: "text-brand-gray" },
                    `₱${parseFloat(
                        row.original[type.name] || 0
                    ).toLocaleString()}`
                ),
        });
    });

    return cols;
});

const table = useVueTable({
    get data() {
        return props.tableData;
    },
    get columns() {
        return columns.value;
    },
    getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
    <Head title="Earnings Report" />
    <div class="space-y-6 text-white">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
        >
            <div>
                <h1 class="text-3xl font-bold text-brand-blue">
                    Earnings Report
                </h1>
                <p class="text-brand-gray text-sm">
                    Financial distribution grouped by period.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex flex-col">
                    <label
                        class="text-[10px] uppercase text-brand-gray font-bold mb-1"
                        >Export</label
                    >
                    <div class="flex gap-1">
                        <button
                            @click="exportReport('csv')"
                            class="bg-brand-light-black border border-brand-border-black hover:bg-brand-blue/20 px-3 py-2 rounded-lg text-xs"
                        >
                            CSV
                        </button>
                        <button
                            @click="exportReport('xlsx')"
                            class="bg-brand-light-black border border-brand-border-black hover:bg-brand-blue/20 px-3 py-2 rounded-lg text-xs"
                        >
                            Excel
                        </button>
                        <button
                            @click="exportReport('pdf')"
                            class="bg-brand-light-black border border-brand-border-black hover:bg-brand-blue/20 px-3 py-2 rounded-lg text-xs"
                        >
                            PDF
                        </button>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label
                        class="text-[10px] uppercase text-brand-gray font-bold mb-1"
                        >View Mode</label
                    >
                    <select
                        @change="handleFilterChange"
                        :value="currentFilter || 'daily'"
                        class="bg-brand-light-black border border-brand-border-black text-white rounded-lg px-4 py-2 text-sm outline-none"
                    >
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <div
                    class="bg-brand-blue/20 border border-brand-blue/50 p-4 rounded-xl min-w-[200px]"
                >
                    <p class="text-xs uppercase text-brand-blue font-bold">
                        Total Collected
                    </p>
                    <p class="text-2xl font-black">
                        ₱{{ totalCollected.toLocaleString() }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-brand-dark-black rounded-2xl p-6 border border-brand-border-black shadow-2xl"
        >
            <div
                class="rounded-xl border border-brand-border-black overflow-x-auto bg-brand-light-black/20"
            >
                <Table>
                    <TableHeader class="bg-brand-light-black">
                        <TableRow
                            v-for="headerGroup in table.getHeaderGroups()"
                            :key="headerGroup.id"
                            class="border-b border-brand-border-black"
                        >
                            <TableHead
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                class="text-white font-bold uppercase text-[10px] p-4"
                            >
                                <FlexRender
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
                            class="border-b border-brand-border-black hover:bg-brand-blue/5 transition-colors"
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                class="p-4"
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
</template>
