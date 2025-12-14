<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Custom Header -->
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900">
                        Dashboard Overview
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Selamat datang kembali, {{ $page.props.auth.user.name }}. Berikut ringkasan aktivitas bisnis Anda.
                    </p>
                </div>
                <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                    {{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- STATS OVERVIEW -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Card 1: Total Sales -->
                    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                        <dt>
                            <div class="absolute rounded-md bg-blue-500 p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Penjualan (Approved)</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.sales_total || 0) }}</p>
                        </dd>
                    </div>

                    <!-- Card 2: Pending Purchase -->
                    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                        <dt>
                            <div class="absolute rounded-md bg-purple-500 p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Pending Purchase</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.purchase_pending || 0 }} <span class="text-sm text-gray-500 font-normal">Orders</span></p>
                        </dd>
                    </div>

                    <!-- Card 3: Total Products -->
                    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                        <dt>
                            <div class="absolute rounded-md bg-indigo-500 p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3.25h3m-3-3.75h3m-12-3h15.75c.828 0 1.5.672 1.5 1.5v2.25c0 .828-.672 1.5-1.5 1.5h-15.75c-.828 0-1.5-.672-1.5-1.5v-2.25c0-.828.672-1.5 1.5-1.5z" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Produk</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.products_count || 0 }} <span class="text-sm text-gray-500 font-normal">Items</span></p>
                        </dd>
                    </div>

                    <!-- Card 4: Unpaid Invoices -->
                    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                        <dt>
                            <div class="absolute rounded-md bg-orange-500 p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Unpaid Invoices</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ stats.unpaid_invoices || 0 }} <span class="text-sm text-gray-500 font-normal">Bills</span></p>
                        </dd>
                    </div>
                </div>

                <!-- MODULES GRID -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aplikasi & Modul</h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        
                        <!-- Sales -->
                        <Link :href="route('sales.index')" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-lg hover:ring-blue-500/50">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Sales</h3>
                                </div>
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                                    Kelola penawaran, pesanan penjualan, dan data pelanggan. Pantau performa penjualan secara real-time.
                                </p>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 mt-auto border-t border-gray-100 group-hover:bg-blue-50 transition-colors">
                                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Buka Modul &rarr;</span>
                            </div>
                        </Link>

                        <!-- Purchase -->
                        <Link :href="route('purchase.index')" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-lg hover:ring-purple-500/50">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Purchase</h3>
                                </div>
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                                    Kelola vendor, RFQ, dan Purchase Orders. Kontrol pengadaan barang dengan efisien.
                                </p>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 mt-auto border-t border-gray-100 group-hover:bg-purple-50 transition-colors">
                                <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Buka Modul &rarr;</span>
                            </div>
                        </Link>

                        <!-- Inventory -->
                        <Link :href="route('inventory')" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-lg hover:ring-indigo-500/50">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3.25h3m-3-3.75h3m-12-3h15.75c.828 0 1.5.672 1.5 1.5v2.25c0 .828-.672 1.5-1.5 1.5h-15.75c-.828 0-1.5-.672-1.5-1.5v-2.25c0-.828.672-1.5 1.5-1.5z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Inventory</h3>
                                </div>
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                                    Monitoring stok, pergerakan barang, dan manajemen gudang. Pastikan stok selalu tersedia.
                                </p>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 mt-auto border-t border-gray-100 group-hover:bg-indigo-50 transition-colors">
                                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Buka Modul &rarr;</span>
                            </div>
                        </Link>

                        <!-- Manufacturing -->
                        <Link :href="route('manufacturing')" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-lg hover:ring-orange-500/50">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600 transition-colors">Manufacturing</h3>
                                </div>
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                                    Kelola Work Order, Bill of Materials (BoM), dan proses produksi barang.
                                </p>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 mt-auto border-t border-gray-100 group-hover:bg-orange-50 transition-colors">
                                <span class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Buka Modul &rarr;</span>
                            </div>
                        </Link>

                        <!-- Invoicing -->
                        <Link :href="route('invoices.index')" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-lg hover:ring-teal-500/50">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-teal-600 transition-colors">Invoicing</h3>
                                </div>
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                                    Pusat tagihan pelanggan dan vendor. Kelola pembayaran dan arus kas.
                                </p>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 mt-auto border-t border-gray-100 group-hover:bg-teal-50 transition-colors">
                                <span class="text-xs font-semibold text-teal-600 uppercase tracking-wider">Buka Modul &rarr;</span>
                            </div>
                        </Link>

                        <!-- Employee -->
                        <Link :href="route('employee')" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-lg hover:ring-pink-500/50">
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-50 text-pink-600 group-hover:bg-pink-600 group-hover:text-white transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Employee</h3>
                                </div>
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed">
                                    Database karyawan, struktur organisasi, dan manajemen SDM.
                                </p>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 mt-auto border-t border-gray-100 group-hover:bg-pink-50 transition-colors">
                                <span class="text-xs font-semibold text-pink-600 uppercase tracking-wider">Buka Modul &rarr;</span>
                            </div>
                        </Link>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
