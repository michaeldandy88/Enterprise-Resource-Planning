<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  boms: Array,
  default_planned_date: String,
});

const form = useForm({
  bom_id: '',
  qty_to_produce: '',
  planned_date: props.default_planned_date || '',
  note: '',
});

const submit = () => {
  form.post(route('manufacturing-orders.store'));
};
</script>

<template>
  <Head title="Create Manufacturing Order" />

  <AuthenticatedLayout>
    <template #header>
      <div>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Create Manufacturing Order
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Buat perintah produksi berdasarkan BOM yang sudah aktif.
        </p>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-xl rounded-lg bg-white p-6 shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- BOM -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              BOM
            </label>
            <select
              v-model="form.bom_id"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="">-- Pilih BOM --</option>
              <option
                v-for="bom in boms"
                :key="bom.id"
                :value="bom.id"
              >
                {{ bom.name }} — ({{ bom.product?.name }})
              </option>
            </select>
            <div
              v-if="form.errors.bom_id"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.bom_id }}
            </div>
          </div>

          <!-- Qty to Produce -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Quantity to Produce
            </label>
            <input
              type="number"
              step="0.0001"
              v-model="form.qty_to_produce"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div
              v-if="form.errors.qty_to_produce"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.qty_to_produce }}
            </div>
          </div>

          <!-- Planned Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Planned Date
            </label>
            <input
              type="date"
              v-model="form.planned_date"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div
              v-if="form.errors.planned_date"
              class="mt-1 text-xs text-red-600"
            >
              {{ form.errors.planned_date }}
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
              :href="route('manufacturing')"
              class="text-sm text-gray-600 hover:underline"
            >
              ← Kembali ke daftar MO
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
            >
              Simpan MO
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
