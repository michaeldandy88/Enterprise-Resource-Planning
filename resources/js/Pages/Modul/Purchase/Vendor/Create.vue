<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
});

const submit = () => {
    form.post(route('vendors.store'));
};
</script>

<template>
    <Head title="Tambah Vendor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Tambah Vendor
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftarkan supplier baru.
                    </p>
                </div>
                <Link
                    :href="route('vendors.index')"
                    class="text-sm text-gray-600 hover:underline"
                >
                    Lihat daftar vendor
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nama Vendor</label>
                            <input v-model="form.name" type="text" class="w-full rounded border-gray-300 text-sm" required autofocus />
                            <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="form.email" type="email" class="w-full rounded border-gray-300 text-sm" />
                            <div v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Telepon</label>
                            <input v-model="form.phone" type="text" class="w-full rounded border-gray-300 text-sm" />
                            <div v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <Link :href="route('vendors.index')" class="text-sm text-gray-600 hover:underline">Batal</Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                Simpan Vendor
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
