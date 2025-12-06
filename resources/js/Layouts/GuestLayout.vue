<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-center items-center pt-6 sm:pt-0">
        <!-- Background gradient -->
        <div class="pointer-events-none fixed inset-0 -z-10 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
        <div class="pointer-events-none fixed inset-0 -z-10 opacity-40" style="background-image: radial-gradient(circle at 0 0, #22c55e22 0, transparent 55%), radial-gradient(circle at 100% 0, #22d3ee26 0, transparent 55%);"></div>

        <div>
            <Link href="/" class="flex items-center gap-3 mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/40">
                    <span class="text-xl font-semibold text-emerald-400">ERP</span>
                </div>
                <div>
                    <p class="text-lg font-semibold tracking-wide text-slate-100">
                        Enterprise Resource Planning
                    </p>
                    <p class="text-xs text-slate-400">
                        Centralized control system
                    </p>
                </div>
            </Link>
        </div>

        <div class="w-full overflow-hidden bg-slate-900/70 border border-slate-800 px-6 py-4 shadow-xl shadow-black/40 sm:max-w-md sm:rounded-xl backdrop-blur-sm">
            <slot />
        </div>
    </div>
</template>
