<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    transactions: Object,
    stocks: Array,
});
</script>

<template>
    <Head title="Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Inventory — Stock Transactions
            </h2>

            <div class="flex gap-2">
                <Link
                    :href="route('products.create')"
                    class="rounded bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-800 hover:bg-gray-300"
                >
                    + Product
                </Link>
                <Link
                    :href="route('locations.create')"
                    class="rounded bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-800 hover:bg-gray-300"
                >
                    + Location
                </Link>
                <Link
                    :href="route('stock-transactions.create')"
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Create Transaction
                </Link>
            </div>
        </template>

        <div class="p-6 space-y-6">
            <!-- STOCK LEVELS -->
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Current Stock Levels</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="stock in stocks"
                        :key="stock.id"
                        class="rounded border p-4"
                    >
                        <div class="font-bold text-gray-800">{{ stock.name }}</div>
                        <div class="text-xs text-gray-500 mb-2">{{ stock.code }}</div>
                        <div class="text-2xl font-semibold" :class="stock.current_stock < 0 ? 'text-red-600' : 'text-blue-600'">
                            {{ Number(stock.current_stock).toLocaleString() }}
                            <span class="text-sm text-gray-500 font-normal">{{ stock.uom }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TRANSACTIONS -->
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Transaction History</h3>
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b font-semibold text-gray-700">
                            <th class="py-2">Tanggal</th>
                            <th>Produk</th>
                            <th>Lokasi</th>
                            <th>Jenis</th>
                            <th>Qty</th>
                            <th>Referensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="trx in transactions.data" :key="trx.id" class="border-b">
                            <td class="py-2">{{ trx.trx_date }}</td>
                            <td>{{ trx.product?.name }}</td>
                            <td>{{ trx.location?.name }}</td>
                            <td>
                                <span
                                    :class="trx.trx_type === 'IN'
                                        ? 'text-green-600 font-semibold'
                                        : 'text-red-600 font-semibold'"
                                >
                                    {{ trx.trx_type }}
                                </span>
                            </td>
                            <td>{{ trx.qty }}</td>
                            <td>{{ trx.reference }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- PAGINATION -->
                <div class="mt-4 flex justify-between">
                    <button
                        :disabled="!transactions.prev_page_url"
                        @click="$inertia.visit(transactions.prev_page_url)"
                        class="rounded bg-gray-200 px-3 py-1 disabled:opacity-40"
                    >
                        Prev
                    </button>

                    <button
                        :disabled="!transactions.next_page_url"
                        @click="$inertia.visit(transactions.next_page_url)"
                        class="rounded bg-gray-200 px-3 py-1 disabled:opacity-40"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
