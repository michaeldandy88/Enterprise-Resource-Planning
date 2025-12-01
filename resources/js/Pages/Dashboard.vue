<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Header di bagian atas (kalau layout kamu support slot header) -->
        <template #header>
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-gray-800">
                    ERP Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih modul untuk mulai mengelola proses bisnis di sistem ERP.
                </p>
            </div>
        </template>

        <!-- Konten utama dashboard -->
        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Welcome box -->
                <div class="mb-8 overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Selamat datang, {{ $page.props.auth.user.name }} 👋
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Berikut adalah modul utama dalam sistem ERP. Silakan pilih modul
                        sesuai kebutuhan operasional perusahaan.
                    </p>
                </div>

                <!-- Grid modul ERP -->
                <div
                    class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
                >
                    <!-- Manufacturing -->
                    <Link
                        href="/manufacturing"
                        class="block h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md"
                    >
                        <div class="mb-3 text-3xl">🏭</div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Manufacturing
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Mengelola proses produksi, work order, dan perencanaan kebutuhan bahan (BOM).
                        </p>
                        <p class="mt-4 text-xs font-semibold uppercase text-blue-600">
                            Lihat modul →
                        </p>
                    </Link>

                    <!-- Inventory -->
                    <Link
                        href="/inventory"
                        class="block h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md"
                    >
                        <div class="mb-3 text-3xl">📦</div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Inventory
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Monitoring stok barang, pergerakan barang masuk/keluar, dan penyesuaian stok.
                        </p>
                        <p class="mt-4 text-xs font-semibold uppercase text-blue-600">
                            Lihat modul →
                        </p>
                    </Link>

                    <!-- Purchase -->
                    <Link
                        href="/purchase"
                        class="block h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md"
                    >
                        <div class="mb-3 text-3xl">🛒</div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Purchase
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Mengelola permintaan pembelian, purchase order, dan penerimaan barang dari supplier.
                        </p>
                        <p class="mt-4 text-xs font-semibold uppercase text-blue-600">
                            Lihat modul →
                        </p>
                    </Link>

                    <!-- Sales -->
                    <Link
                        href="/sales"
                        class="block h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md"
                    >
                        <div class="mb-3 text-3xl">📈</div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Sales
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Mengelola penjualan, sales order, pengiriman barang, dan histori transaksi pelanggan.
                        </p>
                        <p class="mt-4 text-xs font-semibold uppercase text-blue-600">
                            Lihat modul →
                        </p>
                    </Link>

                    <!-- Invoicing -->
                    <Link
                        href="/invoicing"
                        class="block h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md"
                    >
                        <div class="mb-3 text-3xl">🧾</div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Invoicing
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Pembuatan invoice, pencatatan pembayaran, dan monitoring piutang pelanggan.
                        </p>
                        <p class="mt-4 text-xs font-semibold uppercase text-blue-600">
                            Lihat modul →
                        </p>
                    </Link>

                    <!-- Employee -->
                    <Link
                        href="/employee"
                        class="block h-full rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md"
                    >
                        <div class="mb-3 text-3xl">👥</div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Employee
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Manajemen data karyawan, divisi, jabatan, dan informasi terkait SDM.
                        </p>
                        <p class="mt-4 text-xs font-semibold uppercase text-blue-600">
                            Lihat modul →
                        </p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
