<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  code: '',
  name: '',
  description: '',
});

const submit = () => {
  form.post(route('locations.store'));
};
</script>

<template>
  <Head title="Create Location" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Create Location
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Tambahkan lokasi gudang / penyimpanan untuk modul Inventory.
          </p>
        </div>

        <Link
          :href="route('locations.index')"
          class="text-sm text-gray-600 hover:underline"
        >
          Lihat daftar lokasi
        </Link>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Code -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Kode Lokasi
            </label>
            <input
              v-model="form.code"
              type="text"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div v-if="form.errors.code" class="mt-1 text-xs text-red-600">
              {{ form.errors.code }}
            </div>
          </div>

          <!-- Name -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Nama Lokasi
            </label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">
              {{ form.errors.name }}
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Deskripsi (opsional)
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full rounded border-gray-300 text-sm"
            ></textarea>
            <div
              v-if="form.errors.description"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.description }}
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex items-center justify-between pt-2">
            <Link
              :href="route('inventory')"
              class="text-sm text-gray-600 hover:underline"
            >
              ← Kembali ke Inventory
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
            >
              Simpan Lokasi
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
