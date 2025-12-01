<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  products: Array,
  locations: Array,
  today: String,
});

const form = useForm({
  product_id: '',
  location_id: '',
  trx_type: 'IN',
  qty: '',
  trx_date: props.today || '',
  reference: '',
  note: '',
});

const submit = () => {
  form.post(route('stock-transactions.store'));
};
</script>

<template>
  <Head title="Create Stock Transaction" />

  <AuthenticatedLayout>
    <template #header>
      <div>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Create Stock Transaction
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Input penyesuaian stok (IN / OUT) untuk inventory.
        </p>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Product -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Product
            </label>
            <select
              v-model="form.product_id"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="">-- Pilih Produk --</option>
              <option
                v-for="p in products"
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

          <!-- Location -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Location
            </label>
            <select
              v-model="form.location_id"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="">-- Pilih Lokasi --</option>
              <option
                v-for="loc in locations"
                :key="loc.id"
                :value="loc.id"
              >
                {{ loc.code }} - {{ loc.name }}
              </option>
            </select>
            <div
              v-if="form.errors.location_id"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.location_id }}
            </div>
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Transaction Type
            </label>
            <select
              v-model="form.trx_type"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="IN">IN (Masuk)</option>
              <option value="OUT">OUT (Keluar)</option>
            </select>
            <div
              v-if="form.errors.trx_type"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.trx_type }}
            </div>
          </div>

          <!-- Qty -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Quantity
            </label>
            <input
              type="number"
              step="0.001"
              v-model="form.qty"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div
              v-if="form.errors.qty"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.qty }}
            </div>
          </div>

          <!-- Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Transaction Date
            </label>
            <input
              type="date"
              v-model="form.trx_date"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div
              v-if="form.errors.trx_date"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.trx_date }}
            </div>
          </div>

          <!-- Reference -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Reference (optional)
            </label>
            <input
              type="text"
              v-model="form.reference"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div
              v-if="form.errors.reference"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.reference }}
            </div>
          </div>

          <!-- Note -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Note (optional)
            </label>
            <textarea
              v-model="form.note"
              rows="3"
              class="w-full rounded border-gray-300 text-sm"
            ></textarea>
            <div
              v-if="form.errors.note"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.note }}
            </div>
          </div>

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
              Simpan Transaksi
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
