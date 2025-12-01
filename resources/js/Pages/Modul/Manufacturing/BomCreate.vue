<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  finished_products: Array,
});

const form = useForm({
  product_id: '',
  name: '',
  is_active: true,
});

const submit = () => {
  form.post(route('boms.store'));
};
</script>

<template>
  <Head title="Create BOM" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Create Bill of Materials (BOM)
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Buat BOM untuk produk jadi yang akan digunakan di Manufacturing Order.
          </p>
        </div>

        <Link
          :href="route('manufacturing')"
          class="text-sm text-gray-600 hover:underline"
        >
          ← Kembali ke Manufacturing
        </Link>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Product (Finished Goods) -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Produk Jadi
            </label>
            <select
              v-model="form.product_id"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="">-- Pilih Produk Jadi --</option>
              <option
                v-for="p in finished_products"
                :key="p.id"
                :value="p.id"
              >
                {{ p.code }} - {{ p.name }}
              </option>
            </select>
            <div
              v-if="form.errors.product_id"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.product_id }}
            </div>
          </div>

          <!-- BOM Name -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Nama BOM
            </label>
            <input
              type="text"
              v-model="form.name"
              class="w-full rounded border-gray-300 text-sm"
              placeholder="Contoh: BOM Produk A v1"
            />
            <div
              v-if="form.errors.name"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.name }}
            </div>
          </div>

          <!-- Active -->
          <div class="flex items-center gap-2">
            <input
              id="is_active"
              type="checkbox"
              v-model="form.is_active"
              class="rounded border-gray-300"
            />
            <label for="is_active" class="text-sm text-gray-700">
              Aktif
            </label>
          </div>

          <!-- Buttons -->
          <div class="flex items-center justify-between pt-2">
            <Link
              :href="route('manufacturing')"
              class="text-sm text-gray-600 hover:underline"
            >
              ← Batal
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
            >
              Simpan BOM
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
