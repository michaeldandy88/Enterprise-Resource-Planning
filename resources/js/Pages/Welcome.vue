<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('hero-illustration')?.classList.add('hidden');
}
</script>

<template>
    <Head title="ERP System" />

    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
        <!-- Background gradient -->
        <div
            class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"
        ></div>
        <div
            class="pointer-events-none fixed inset-0 -z-10 opacity-40"
            style="
                background-image: radial-gradient(circle at 0 0, #22c55e22 0, transparent 55%),
                    radial-gradient(circle at 100% 0, #22d3ee26 0, transparent 55%);
            "
        ></div>

        <!-- NAVBAR -->
        <header class="border-b border-slate-800/60 bg-slate-950/60 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/40"
                    >
                        <span class="text-lg font-semibold text-emerald-400">ERP</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-wide text-slate-100">
                            Enterprise Resource Planning
                        </p>
                        <p class="text-xs text-slate-400">
                            Centralized control for your business operations
                        </p>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-100 ring-1 ring-slate-700 hover:ring-emerald-500 hover:text-emerald-300 transition"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-slate-100 ring-1 ring-slate-700 hover:ring-emerald-500 hover:text-emerald-300 transition"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 hover:bg-emerald-400 transition"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1">
            <div class="mx-auto flex max-w-6xl flex-col gap-12 px-6 py-10 lg:flex-row lg:items-center">
                <!-- LEFT: HERO TEXT -->
                <section class="flex-1 space-y-6">
                    <p class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/5 px-3 py-1 text-xs font-medium text-emerald-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Real-time control for Finance, Inventory, HR & Sales
                    </p>

                    <div class="space-y-4">
                        <h1 class="text-3xl font-semibold tracking-tight text-slate-50 sm:text-4xl lg:text-5xl">
                            ERP Dashboard untuk
                            <span class="text-emerald-400">mengelola bisnis</span>
                            dalam satu tempat.
                        </h1>
                        <p class="max-w-xl text-sm leading-relaxed text-slate-300 sm:text-base">
                            Pantau stok, penjualan, keuangan, dan aktivitas karyawan dari satu
                            sistem terintegrasi. Kurangi pekerjaan manual, tingkatkan akurasi
                            data, dan ambil keputusan lebih cepat.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            v-if="canLogin"
                            :href="$page.props.auth.user ? route('dashboard') : route('login')"
                            class="inline-flex items-center justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 hover:bg-emerald-400 transition"
                        >
                            {{ $page.props.auth.user ? 'Masuk ke Dashboard' : 'Masuk ke Sistem' }}
                            <svg
                                class="ml-2 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4.5 12h15m0 0-6.75-6.75M19.5 12l-6.75 6.75"
                                />
                            </svg>
                        </Link>

                        <a
                            href="#modules"
                            class="inline-flex items-center justify-center rounded-lg border border-slate-600 px-4 py-2.5 text-sm font-medium text-slate-200 hover:border-emerald-500 hover:text-emerald-300 transition"
                        >
                            Lihat modul utama
                        </a>
                    </div>

                    <!-- QUICK STATS -->
                    <div class="mt-4 grid grid-cols-2 gap-4 text-xs sm:text-sm md:grid-cols-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400">
                                Transaksi / hari
                            </p>
                            <p class="mt-1 text-lg font-semibold text-slate-50">1.250+</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400">
                                Akurasi stok
                            </p>
                            <p class="mt-1 text-lg font-semibold text-slate-50">99.2%</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400">
                                Cabang aktif
                            </p>
                            <p class="mt-1 text-lg font-semibold text-slate-50">12</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-slate-400">
                                Pengguna
                            </p>
                            <p class="mt-1 text-lg font-semibold text-slate-50">180+</p>
                        </div>
                    </div>
                </section>

                <!-- RIGHT: ILLUSTRATION / PREVIEW -->
                <section class="flex-1">
                    <div
                        id="hero-illustration"
                        class="relative rounded-2xl border border-slate-800 bg-slate-900/70 p-4 shadow-xl shadow-black/40"
                    >
                        <div class="flex items-center justify-between pb-3">
                            <div class="flex gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-500/80"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-400/80"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                            </div>
                            <p class="text-xs text-slate-400">Preview ERP Dashboard</p>
                        </div>

                        <div class="grid gap-3 md:grid-cols-5">
                            <!-- Sidebar -->
                            <div class="space-y-2 rounded-xl bg-slate-950/70 p-2 md:col-span-2">
                                <p class="mb-2 text-xs font-semibold text-slate-300">
                                    Modul
                                </p>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between rounded-lg bg-emerald-500/10 px-2 py-1.5">
                                        <span class="text-emerald-300">Dashboard</span>
                                        <span class="text-[10px] text-emerald-300">Live</span>
                                    </div>
                                    <div class="rounded-lg px-2 py-1.5 text-slate-300/90">
                                        Inventory
                                    </div>
                                    <div class="rounded-lg px-2 py-1.5 text-slate-300/90">
                                        Penjualan
                                    </div>
                                    <div class="rounded-lg px-2 py-1.5 text-slate-300/90">
                                        Keuangan
                                    </div>
                                    <div class="rounded-lg px-2 py-1.5 text-slate-300/90">
                                        HR &amp; Payroll
                                    </div>
                                </div>
                            </div>

                            <!-- Main widgets -->
                            <div class="space-y-3 md:col-span-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-xl bg-slate-950/70 p-3">
                                        <p class="text-[11px] text-slate-400">
                                            Nilai penjualan hari ini
                                        </p>
                                        <p class="mt-1 text-lg font-semibold text-emerald-400">
                                            Rp 128.450.000
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-slate-950/70 p-3">
                                        <p class="text-[11px] text-slate-400">
                                            Faktur belum dibayar
                                        </p>
                                        <p class="mt-1 text-lg font-semibold text-amber-300">
                                            34
                                        </p>
                                    </div>
                                </div>

                                <div class="rounded-xl bg-slate-950/70 p-3">
                                    <p class="mb-2 text-[11px] text-slate-400">
                                        Pergerakan stok 7 hari terakhir
                                    </p>
                                    <div class="h-24 rounded-lg border border-dashed border-slate-700/70"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- MODULES SECTION -->
            <section id="modules" class="border-t border-slate-800/70 bg-slate-950/70">
                <div class="mx-auto max-w-6xl px-6 py-10">
                    <h2 class="text-base font-semibold text-slate-100">
                        Modul utama sistem ERP
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        Beberapa contoh fitur yang bisa kamu kembangkan di aplikasi ini.
                    </p>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                            <p class="text-sm font-semibold text-slate-100">
                                Inventory &amp; Gudang
                            </p>
                            <p class="text-xs text-slate-400">
                                Pantau stok masuk/keluar, multi gudang, stok minimum, dan
                                notifikasi restock.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                            <p class="text-sm font-semibold text-slate-100">
                                Penjualan &amp; Pembelian
                            </p>
                            <p class="text-xs text-slate-400">
                                Kelola SO, PO, invoice, dan integrasi ke laporan keuangan.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                            <p class="text-sm font-semibold text-slate-100">
                                Keuangan
                            </p>
                            <p class="text-xs text-slate-400">
                                Jurnal umum, arus kas, piutang, hutang, dan ringkasan profit.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                            <p class="text-sm font-semibold text-slate-100">
                                HR &amp; Payroll
                            </p>
                            <p class="text-xs text-slate-400">
                                Data karyawan, absensi, lembur, dan perhitungan gaji otomatis.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-slate-800/70 py-4 text-center text-xs text-slate-500">
            ERP System &middot; Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
        </footer>
    </div>
</template>
