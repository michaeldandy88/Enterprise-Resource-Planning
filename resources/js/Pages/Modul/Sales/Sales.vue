<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  orders: Array
});
</script>

<template>
  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">

        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Sales Orders
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Daftar pesanan penjualan yang tercatat dalam sistem.
          </p>
        </div>

        <Link
          href="/sales/create"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
          + Buat Sales Order
        </Link>

      </div>
    </template>

    <!-- KONTEN -->
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">

          <!-- Jika tidak ada data -->
          <div v-if="orders.length === 0" class="text-gray-600">
            Belum ada Sales Order.
          </div>

          <!-- TABEL -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
              <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                  <th class="p-3 border">Kode SO</th>
                  <th class="p-3 border">Customer</th>
                  <th class="p-3 border">Total</th>
                  <th class="p-3 border">Status</th>
                  <th class="p-3 border text-center">Aksi</th>
                </tr>
              </thead>

              <tbody class="text-sm">
                <tr
                  v-for="order in orders"
                  :key="order.id"
                  class="hover:bg-gray-50"
                >
                  <td class="p-3 border">{{ order.so_number }}</td>
                  <td class="p-3 border">{{ order.customer?.name }}</td>
                  <td class="p-3 border">Rp {{ order.total_amount }}</td>
                  <td class="p-3 border">{{ order.status }}</td>

                  <td class="p-3 border text-center space-x-2">
                    <Link
                      :href="`/sales/edit/${order.id}`"
                      class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600 text-xs"
                    >
                      Edit
                    </Link>

                    <Link
                      method="delete"
                      :href="`/sales/${order.id}`"
                      as="button"
                      class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700 text-xs"
                    >
                      Hapus
                    </Link>
                  </td>

                </tr>

              </tbody>
            </table>
          </div>

        </div>

      </div>
    </div>

  </AuthenticatedLayout>
</template>
