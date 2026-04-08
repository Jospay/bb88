<script setup>
import { ref, h, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { useVueTable, getCoreRowModel, FlexRender } from "@tanstack/vue-table";

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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";

const props = defineProps({
    users: Object, // Laravel Paginator
});

// Extract the actual data array from the paginator
const data = computed(() => props.users.data);

const columns = [
    {
        accessorKey: "team_name",
        header: "Team Name",
        cell: ({ row }) =>
            h(
                "span",
                { class: "text-white font-bold" },
                row.original.team_name || "No Team Name",
            ),
    },
    {
        accessorKey: "location",
        header: "Location",
        cell: ({ row }) => {
            const address = [
                row.original.barangay,
                row.original.city,
                row.original.province,
            ]
                .filter(Boolean)
                .join(", ");

            return h(
                "span",
                {
                    class: "text-brand-gray text-xs block max-w-[200px]",
                },
                address || "N/A",
            );
        },
    },
    {
        accessorKey: "transaction_status",
        header: "Payment Status",
        cell: ({ row }) => {
            const status = row.original.transaction_status;
            const variants = {
                paid: "bg-green-500",
                pending_payment: "bg-orange-500",
                pending_registration: "bg-blue-500",
                failed: "bg-red-500",
            };
            return h(
                Badge,
                {
                    class: `${variants[status] || "bg-gray-500"} text-white uppercase text-[10px]`,
                },
                () => status?.replace("_", " ") || "Unknown",
            );
        },
    },
    {
        accessorKey: "total_payment",
        header: "Total Paid",
        cell: ({ row }) => {
            const amount = parseFloat(row.original.total_payment);

            const formatted = amount.toLocaleString("en-PH", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            return h("span", { class: "text-white" }, `₱${formatted}`);
        },
    },
    {
        accessorKey: "members_count",
        header: "Members",
        cell: ({ row }) =>
            h(
                "span",
                { class: "text-white" },
                row.original.detail_user?.length || 0,
            ),
    },
    {
        id: "actions",
        header: "Action",
        cell: ({ row }) => {
            return h(Dialog, {}, () => [
                // 1. Dialog Trigger
                h(DialogTrigger, { asChild: true }, () =>
                    h(
                        Button,
                        {
                            variant: "outline",
                            size: "sm",
                            class: "border-brand-blue text-brand-blue hover:bg-brand-blue hover:text-white transition-colors",
                        },
                        () => "View Members",
                    ),
                ),
                // 2. Dialog Content
                h(
                    DialogContent,
                    {
                        class: "sm:max-w-[900px] bg-brand-dark-black text-white border-brand-border-black shadow-2xl",
                    },
                    () => [
                        h(DialogHeader, {}, () => [
                            h(
                                DialogTitle,
                                { class: "text-brand-blue text-2xl font-bold" },
                                () => `Team: ${row.original.team_name}`,
                            ),
                            h(
                                DialogDescription,
                                { class: "text-brand-gray" },
                                () =>
                                    "Review the list of players and items registered under this team.",
                            ),
                        ]),
                        h(
                            "div",
                            {
                                class: "mt-6 overflow-hidden rounded-lg border border-brand-border-black",
                            },
                            [
                                h(Table, {}, () => [
                                    h(
                                        TableHeader,
                                        { class: "bg-brand-light-black" },
                                        () =>
                                            h(
                                                TableRow,
                                                {
                                                    class: "border-brand-border-black hover:bg-transparent",
                                                },
                                                () => [
                                                    h(
                                                        TableHead,
                                                        {
                                                            class: "text-white font-bold",
                                                        },
                                                        () => "Full Name",
                                                    ),
                                                    h(
                                                        TableHead,
                                                        {
                                                            class: "text-white font-bold",
                                                        },
                                                        () => "Account Type",
                                                    ),
                                                    h(
                                                        TableHead,
                                                        {
                                                            class: "text-white font-bold",
                                                        },
                                                        () => "Shirt Size",
                                                    ),
                                                    h(
                                                        TableHead,
                                                        {
                                                            class: "text-white font-bold",
                                                        },
                                                        () => "Claim Status",
                                                    ),
                                                ],
                                            ),
                                    ),
                                    h(TableBody, {}, () =>
                                        row.original.detail_user?.length > 0
                                            ? row.original.detail_user.map(
                                                  (member) =>
                                                      h(
                                                          TableRow,
                                                          {
                                                              key: member.id,
                                                              class: "border-brand-border-black hover:bg-white/5",
                                                          },
                                                          () => [
                                                              h(
                                                                  TableCell,
                                                                  {
                                                                      class: "text-white",
                                                                  },
                                                                  () =>
                                                                      member.full_name,
                                                              ),
                                                              h(
                                                                  TableCell,
                                                                  {},
                                                                  () =>
                                                                      h(
                                                                          Badge,
                                                                          {
                                                                              variant:
                                                                                  "outline",
                                                                              class: "text-brand-blue border-brand-blue",
                                                                          },
                                                                          () =>
                                                                              member.account_type,
                                                                      ),
                                                              ),
                                                              h(
                                                                  TableCell,
                                                                  {
                                                                      class: "text-white font-mono",
                                                                  },
                                                                  () =>
                                                                      member.size_shirt,
                                                              ),
                                                              h(
                                                                  TableCell,
                                                                  {},
                                                                  () =>
                                                                      h(
                                                                          Badge,
                                                                          {
                                                                              class:
                                                                                  member.status ===
                                                                                  "claimed"
                                                                                      ? "bg-brand-green text-white"
                                                                                      : "bg-brand-yellow text-black",
                                                                          },
                                                                          () =>
                                                                              member.status,
                                                                      ),
                                                              ),
                                                          ],
                                                      ),
                                              )
                                            : h(TableRow, {}, () =>
                                                  h(
                                                      TableCell,
                                                      {
                                                          colspan: 4,
                                                          class: "text-center py-10 text-brand-gray",
                                                      },
                                                      () => "No members found.",
                                                  ),
                                              ),
                                    ),
                                ]),
                            ],
                        ),
                    ],
                ),
            ]);
        },
    },
];

const table = useVueTable({
    get data() {
        return data.value;
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
});

const goToPage = (url) => {
    if (url) {
        router.get(url, {}, { preserveState: true, preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Team Management" />
    <div class="space-y-6 text-white p-4">
        <div class="flex flex-col gap-1">
            <h1 class="text-3xl font-bold tracking-tight text-brand-blue">
                Team Management
            </h1>
            <p class="text-brand-gray text-sm">
                Manage tournament teams and their respective members.
            </p>
        </div>

        <div
            class="bg-brand-dark-black rounded-2xl p-6 border border-brand-border-black shadow-2xl"
        >
            <div class="w-full space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <Input
                        placeholder="Search team name..."
                        class="max-w-sm bg-brand-light-black border-brand-border-black text-white"
                    />
                </div>

                <div
                    class="rounded-xl border border-brand-border-black overflow-hidden bg-brand-dark-black"
                >
                    <Table>
                        <TableHeader class="bg-brand-light-black">
                            <TableRow
                                v-for="headerGroup in table.getHeaderGroups()"
                                :key="headerGroup.id"
                                class="hover:bg-transparent"
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
                        <template v-if="table.getRowModel().rows?.length > 0">
                            <TableRow
                                v-for="row in table.getRowModel().rows"
                                :key="row.id"
                                class="border-b border-brand-border-black hover:bg-brand-blue/5 transition-colors"
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
                        </template>

                        <TableRow v-else class="hover:bg-transparent">
                            <TableCell
                                :colspan="columns.length"
                                class="p-10 text-center hover:bg-[#030C21] text-brand-gray italic"
                            >
                                No teams found.
                            </TableCell>
                        </TableRow>
                    </Table>
                </div>

                <div class="flex items-center justify-between px-2 py-4">
                    <div class="text-sm text-brand-gray italic">
                        Showing {{ users.from }} to {{ users.to }} of
                        {{ users.total }} teams
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-brand-gray mr-4">
                            Page {{ users.current_page }} of
                            {{ users.last_page }}
                        </span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="!users.prev_page_url"
                            @click="goToPage(users.prev_page_url)"
                            class="border-brand-border-black bg-brand-light-black text-white hover:text-white hover:bg-brand-blue/20"
                        >
                            Previous
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="!users.next_page_url"
                            @click="goToPage(users.next_page_url)"
                            class="border-brand-border-black bg-brand-light-black text-white hover:text-white hover:bg-brand-blue/20"
                        >
                            Next
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
