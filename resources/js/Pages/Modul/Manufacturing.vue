<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
});
</script>

<template>
    <Head title="Manufacturing" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Manufacturing — Production Orders
            </h2>

            <div class="flex gap-2">
            <Link
            :href="route('boms.index')"
            class="rounded bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-800 hover:bg-gray-300"
            >
            Manage BOM
            </Link>
            <Link
                :href="route('boms.create')"
                class="rounded bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-800 hover:bg-gray-300"
            >
                + Create BOM
            </Link>
            <Link
                :href="route('manufacturing-orders.create')"
                class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            >
                + Create MO
            </Link>
            </div>
        </template>

        <div class="p-6">
            <div class="rounded-lg bg-white p-6 shadow">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b font-semibold text-gray-700">
                            <th class="py-2">Kode</th>
                            <th>Produk</th>
                            <th>BOM</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Planned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="order.id" class="border-b">
                            <td class="py-2">
                                <Link
                                    :href="`/manufacturing-orders/${order.id}`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ order.code }}
                                </Link>
                            </td>
                            <td>{{ order.product?.name }}</td>
                            <td>{{ order.bom?.name }}</td>
                            <td>{{ order.qty_to_produce }}</td>
                            <td>
                                <span
                                    :class="{
                                        'text-green-600 font-semibold': order.status === 'DONE',
                                        'text-orange-600 font-semibold': order.status !== 'DONE',
                                    }"
                                >
                                    {{ order.status }}
                                </span>
                            </td>
                            <td>{{ order.planned_date }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- PAGINATION -->
                <div class="mt-4 flex justify-between">
                    <button
                        :disabled="!orders.prev_page_url"
                        @click="$inertia.visit(orders.prev_page_url)"
                        class="rounded bg-gray-200 px-3 py-1 disabled:opacity-40"
                    >
                        Prev
                    </button>

                    <button
                        :disabled="!orders.next_page_url"
                        @click="$inertia.visit(orders.next_page_url)"
                        class="rounded bg-gray-200 px-3 py-1 disabled:opacity-40"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
