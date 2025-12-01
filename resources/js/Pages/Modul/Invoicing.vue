<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Invoicing" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Invoicing Module
            </h2>
        </template>

        <div class="p-6">
            <div class="rounded-lg bg-white p-6 shadow">
                Ini adalah halaman <strong>Invoicing</strong>.
            </div>
        </div>
    </AuthenticatedLayout>
</template>
