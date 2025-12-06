<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    vendors: Object,
});
</script>

<template>
    <Head title="Vendors" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Vendor / Supplier
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftar rekanan penyedia barang.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="route('purchase.index')"
                        class="rounded bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        &larr; Kembali ke Purchase
                    </Link>
                    <Link
                        :href="route('vendors.create')"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        + Tambah Vendor
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
                                    <th class="p-3 border text-left">Nama Vendor</th>
                                    <th class="p-3 border text-left">Email</th>
                                    <th class="p-3 border text-left">Telepon</th>
                                    <th class="p-3 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr v-for="vendor in vendors.data" :key="vendor.id" class="hover:bg-gray-50">
                                    <td class="p-3 border font-medium text-gray-900">{{ vendor.name }}</td>
                                    <td class="p-3 border text-gray-600">{{ vendor.email || '-' }}</td>
                                    <td class="p-3 border text-gray-600">{{ vendor.phone || '-' }}</td>
                                    <td class="p-3 border text-center">
                                        <div class="flex justify-center gap-2">
                                            <Link :href="route('vendors.edit', vendor.id)" class="text-blue-600 hover:underline text-xs">Edit</Link>
                                            <Link :href="route('vendors.destroy', vendor.id)" method="delete" as="button" class="text-red-600 hover:underline text-xs">Hapus</Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="vendors.data.length === 0">
                                    <td colspan="4" class="p-6 text-center text-gray-500">
                                        Belum ada data vendor.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-4 flex justify-between items-center" v-if="vendors.total > 0">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ vendors.from }} sampai {{ vendors.to }} dari {{ vendors.total }} data.
                        </div>
                        <div class="flex gap-1">
                            <Link
                                v-for="(link, k) in vendors.links"
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
