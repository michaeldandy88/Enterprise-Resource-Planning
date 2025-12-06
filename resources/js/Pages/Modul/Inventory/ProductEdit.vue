<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  product: Object,
});

const form = useForm({
  code: props.product.code,
  name: props.product.name,
  uom: props.product.uom,
  type: props.product.type,
  cost: props.product.cost,
  price: props.product.price,
  is_active: Boolean(props.product.is_active),
});

const submit = () => {
  form.put(route('products.update', props.product.id));
};
</script>

<template>
  <Head title="Edit Product" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Product
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Perbarui data produk {{ product.name }}.
          </p>
        </div>

        <Link
          :href="route('inventory')"
          class="text-sm text-gray-600 hover:underline"
        >
          Lihat daftar produk
        </Link>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Code -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Kode Produk
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
              Nama Produk
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

          <!-- UOM -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Satuan (UOM)
            </label>
            <input
              v-model="form.uom"
              type="text"
              placeholder="PCS, KG, LTR, dst."
              class="w-full rounded border-gray-300 text-sm"
            />
            <div v-if="form.errors.uom" class="mt-1 text-xs text-red-600">
              {{ form.errors.uom }}
            </div>
          </div>

          <!-- Type -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Tipe Produk
            </label>
            <select
              v-model="form.type"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="RAW">RAW (Bahan Baku)</option>
              <option value="FINISHED">FINISHED (Barang Jadi)</option>
            </select>
            <div v-if="form.errors.type" class="mt-1 text-xs text-red-600">
              {{ form.errors.type }}
            </div>
          </div>

          <!-- Cost -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Harga Beli Standar (Cost)
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                <input
                v-model="form.cost"
                type="number"
                class="w-full rounded border-gray-300 text-sm pl-10"
                placeholder="0"
                />
            </div>
            <div v-if="form.errors.cost" class="mt-1 text-xs text-red-600">
              {{ form.errors.cost }}
            </div>
          </div>

          <!-- Price -->
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Harga Jual Standar (Price)
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                <input
                v-model="form.price"
                type="number"
                class="w-full rounded border-gray-300 text-sm pl-10"
                placeholder="0"
                />
            </div>
            <div v-if="form.errors.price" class="mt-1 text-xs text-red-600">
              {{ form.errors.price }}
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
              :href="route('inventory')"
              class="text-sm text-gray-600 hover:underline"
            >
              ← Batal
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
            >
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
