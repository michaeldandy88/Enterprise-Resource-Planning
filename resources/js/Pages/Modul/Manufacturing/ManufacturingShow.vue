<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  mo: Object,
  requirements: Array,
});
</script>

<template>
  <Head :title="`MO ${mo.code}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manufacturing Order — {{ mo.code }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Detail perintah produksi dan kebutuhan bahan baku.
          </p>
        </div>

        <div class="flex gap-2">
          <Link
            :href="route('manufacturing')"
            class="text-sm text-gray-600 hover:underline"
          >
            ← Kembali ke Manufacturing
          </Link>

          <Link
            v-if="mo.status !== 'DONE'"
            :href="route('manufacturing-orders.complete', mo.id)"
            method="post"
            as="button"
            class="rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"
          >
            Complete MO
          </Link>
          <button
            v-else
            disabled
            class="rounded bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 cursor-not-allowed"
          >
            Sudah Selesai
          </button>
        </div>
      </div>
    </template>

    <!-- INFO MO -->
    <div class="p-6">
      <div class="mx-auto max-w-4xl space-y-6">
        <div class="rounded-lg bg-white p-6 shadow">
          <h3 class="mb-3 text-lg font-semibold text-gray-800">
            Informasi Manufacturing Order
          </h3>
          <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
            <div>
              <dt class="font-medium text-gray-600">Kode MO</dt>
              <dd class="text-gray-800">{{ mo.code }}</dd>
            </div>
            <div>
              <dt class="font-medium text-gray-600">Produk Jadi</dt>
              <dd class="text-gray-800">
                {{ mo.product?.name }} ({{ mo.product?.code }})
              </dd>
            </div>
            <div>
              <dt class="font-medium text-gray-600">Qty to Produce</dt>
              <dd class="text-gray-800">
                {{ mo.qty_to_produce }} {{ mo.product?.uom }}
              </dd>
            </div>
            <div>
              <dt class="font-medium text-gray-600">Status</dt>
              <dd>
                <span
                  :class="[
                    'rounded px-2 py-0.5 text-xs font-semibold',
                    mo.status === 'DONE'
                      ? 'bg-green-100 text-green-700'
                      : 'bg-yellow-100 text-yellow-700',
                  ]"
                >
                  {{ mo.status }}
                </span>
              </dd>
            </div>
            <div>
              <dt class="font-medium text-gray-600">Planned Date</dt>
              <dd class="text-gray-800">
                {{ mo.planned_date || "-" }}
              </dd>
            </div>
            <div>
              <dt class="font-medium text-gray-600">Done Date</dt>
              <dd class="text-gray-800">
                {{ mo.done_date || "-" }}
              </dd>
            </div>
            <div class="sm:col-span-2">
              <dt class="font-medium text-gray-600">Note</dt>
              <dd class="text-gray-800">
                {{ mo.note || "-" }}
              </dd>
            </div>
          </dl>
        </div>

        <!-- TABEL KEBUTUHAN BAHAN -->
        <div class="rounded-lg bg-white p-6 shadow">
          <h3 class="mb-3 text-lg font-semibold text-gray-800">
            Kebutuhan Bahan Baku
          </h3>

          <table class="w-full border-collapse text-left text-sm">
            <thead>
              <tr class="border-b font-semibold text-gray-700">
                <th class="py-2">Bahan Baku</th>
                <th>Qty per Unit</th>
                <th>Total Required</th>
                <th>Available Stock</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="req in requirements"
                :key="req.id"
                class="border-b last:border-0"
              >
                <td class="py-2">
                  <div class="font-medium">
                    {{ req.raw_product?.name }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ req.raw_product?.code }}
                  </div>
                </td>
                <td>
                  {{ req.qty_per_unit }} {{ req.raw_product?.uom }}
                </td>
                <td>
                  {{ req.total_required }} {{ req.raw_product?.uom }}
                </td>
                <td>
                  <span :class="req.available_stock < req.total_required ? 'text-red-600 font-bold' : 'text-green-600'">
                    {{ Number(req.available_stock).toLocaleString() }} {{ req.raw_product?.uom }}
                  </span>
                </td>
              </tr>

              <tr v-if="requirements.length === 0">
                <td colspan="3" class="py-4 text-center text-gray-500">
                  BOM untuk MO ini belum memiliki item bahan baku.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
