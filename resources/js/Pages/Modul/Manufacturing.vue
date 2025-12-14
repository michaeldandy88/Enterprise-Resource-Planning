<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};
</script>

<template>
    <Head title="Manufacturing" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Manufacturing
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftar perintah produksi (Manufacturing Orders).
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="route('boms.index')"
                        class="rounded bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Bill of Materials (BoM)
                    </Link>
                    <Link
                        :href="route('manufacturing-orders.create')"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        + Buat Order Produksi
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
                                    <th class="p-3 border text-left">Kode MO</th>
                                    <th class="p-3 border text-left">Produk</th>
                                    <th class="p-3 border text-left">BoM</th>
                                    <th class="p-3 border text-right">Qty</th>
                                    <th class="p-3 border text-center">Status</th>
                                    <th class="p-3 border text-left">Tgl Rencana</th>
                                    <th class="p-3 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                                    <td class="p-3 border font-mono text-blue-600 font-semibold">
                                        <Link :href="route('manufacturing-orders.show', order.id)" class="hover:underline">
                                            {{ order.code }}
                                        </Link>
                                    </td>
                                    <td class="p-3 border font-medium text-gray-900">{{ order.product?.name || '-' }}</td>
                                    <td class="p-3 border text-gray-600">{{ order.bom?.name || '-' }}</td>
                                    <td class="p-3 border text-right font-bold">{{ order.qty_to_produce }}</td>
                                    <td class="p-3 border text-center">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold"
                                            :class="{
                                                'bg-gray-100 text-gray-600': order.status === 'DRAFT',
                                                'bg-blue-100 text-blue-800': order.status === 'CONFIRMED',
                                                'bg-green-100 text-green-800': order.status === 'DONE',
                                                'bg-red-100 text-red-800': order.status === 'CANCELLED'
                                            }"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="p-3 border">{{ formatDate(order.planned_date) }}</td>
                                    <td class="p-3 border text-center">
                                        <Link :href="route('manufacturing-orders.show', order.id)" class="text-blue-600 hover:underline text-xs">
                                            Detail
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="orders.data.length === 0">
                                    <td colspan="7" class="p-6 text-center text-gray-500">
                                        Belum ada data order produksi.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-4 flex justify-between items-center" v-if="orders.total > 0">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ orders.from }} sampai {{ orders.to }} dari {{ orders.total }} data.
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="(link, k) in orders.links"
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
