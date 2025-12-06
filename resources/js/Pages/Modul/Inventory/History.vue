<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    transactions: Object,
});

// Helper untuk format tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

// Helper untuk format angka
const formatNumber = (value) => {
    return new Intl.NumberFormat('id-ID').format(value);
};
</script>

<template>
    <Head title="Riwayat Stok" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Inventory / Riwayat Stok
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Log pergerakan barang masuk dan keluar.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="route('inventory')"
                        class="rounded bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        &larr; Kembali ke Inventory
                    </Link>
                    <Link
                        :href="route('stock-transactions.create')"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        + Transaksi Manual
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    
                    <!-- TABEL -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded">
                            <thead class="bg-gray-100 text-sm text-gray-700">
                                <tr>
                                    <th class="p-3 border text-left">Tanggal</th>
                                    <th class="p-3 border text-left">Produk</th>
                                    <th class="p-3 border text-left">Lokasi</th>
                                    <th class="p-3 border text-center">Jenis</th>
                                    <th class="p-3 border text-right">Qty</th>
                                    <th class="p-3 border text-left">Referensi</th>
                                    <th class="p-3 border text-left">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr v-for="trx in transactions.data" :key="trx.id" class="hover:bg-gray-50">
                                    <td class="p-3 border">{{ formatDate(trx.trx_date) }}</td>
                                    <td class="p-3 border font-medium">{{ trx.product?.name || '-' }}</td>
                                    <td class="p-3 border">{{ trx.location?.name || '-' }}</td>
                                    <td class="p-3 border text-center">
                                        <span
                                            :class="trx.trx_type === 'IN'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'"
                                            class="px-2 py-1 rounded text-xs font-bold"
                                        >
                                            {{ trx.trx_type }}
                                        </span>
                                    </td>
                                    <td class="p-3 border text-right font-mono">{{ formatNumber(trx.qty) }}</td>
                                    <td class="p-3 border text-gray-600">{{ trx.reference || '-' }}</td>
                                    <td class="p-3 border text-gray-500 text-xs italic">{{ trx.note || '-' }}</td>
                                </tr>
                                <tr v-if="transactions.data.length === 0">
                                    <td colspan="7" class="p-6 text-center text-gray-500">
                                        Belum ada data transaksi stok.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-4 flex justify-between items-center" v-if="transactions.total > 0">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ transactions.from }} sampai {{ transactions.to }} dari {{ transactions.total }} data.
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="(link, k) in transactions.links"
                                :key="k"
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="px-3 py-1 border rounded text-sm"
                                :class="{
                                    'bg-blue-600 text-white border-blue-600': link.active,
                                    'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                    'opacity-50 cursor-not-allowed': !link.url
                                }"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
