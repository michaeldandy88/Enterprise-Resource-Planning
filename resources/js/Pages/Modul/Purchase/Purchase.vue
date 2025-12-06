<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  orders: Array
});

// Status label styling
function statusClass(status) {
  switch (status) {
    case "RFQ":
      return "bg-gray-200 text-gray-700";
    case "Sent":
      return "bg-blue-200 text-blue-700";
    case "Purchase Order":
      return "bg-yellow-200 text-yellow-700";
    case "Received":
      return "bg-green-200 text-green-700";
    case "Cancelled":
      return "bg-red-200 text-red-700";
    default:
      return "bg-gray-200 text-gray-700";
  }
}
</script>

<template>
  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">
            Purchase Orders
          </h2>
          <p class="text-sm text-gray-500">Daftar pembelian dari vendor.</p>
        </div>

        <Link
          href="/purchase/create"
          class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
        >
          + Create Purchase Order
        </Link>
      </div>
    </template>

    <!-- CONTENT -->
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <div v-if="orders.length === 0" class="text-gray-500">
            Belum ada Purchase Order.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded">
              <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                  <th class="border p-3">PO Number</th>
                  <th class="border p-3">Supplier</th>
                  <th class="border p-3">Date</th>
                  <th class="border p-3">Status</th>
                  <th class="border p-3">Total</th>
                  <th class="border p-3 text-center">Actions</th>
                </tr>
              </thead>

              <tbody class="text-sm">
                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">

                  <td class="border p-3">{{ order.po_number }}</td>
                  <td class="border p-3">{{ order.supplier?.name }}</td>
                  <td class="border p-3">{{ order.order_date }}</td>

                  <!-- STATUS BADGE -->
                  <td class="border p-3">
                    <span
                      class="px-2 py-1 rounded text-xs font-semibold"
                      :class="statusClass(order.status)"
                    >
                      {{ order.status }}
                    </span>
                  </td>

                  <td class="border p-3">Rp {{ order.total_amount }}</td>

                  <!-- ACTION BUTTONS -->
                  <td class="border p-3 text-center space-x-2">

                    <!-- EDIT (Hanya RFQ) -->
                    <Link
                      v-if="order.status === 'RFQ'"
                      :href="`/purchase/edit/${order.id}`"
                      class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600 text-xs"
                    >
                      Edit
                    </Link>

                    <!-- SEND RFQ -->
                    <Link
                      v-if="order.status === 'RFQ'"
                      :href="route('purchase.send', order.id)"
                      method="post"
                      as="button"
                      class="rounded bg-blue-500 px-3 py-1 text-white hover:bg-blue-600 text-xs"
                    >
                      Send RFQ
                    </Link>

                    <!-- CONFIRM -->
                    <Link
                      v-if="['RFQ', 'Sent'].includes(order.status)"
                      :href="route('purchase.confirm', order.id)"
                      method="post"
                      as="button"
                      class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700 text-xs"
                    >
                      Confirm
                    </Link>

                    <!-- RECEIVE -->
                    <Link
                      v-if="order.status === 'Purchase Order'"
                      :href="route('purchase.receive', order.id)"
                      class="rounded bg-purple-600 px-3 py-1 text-white hover:bg-purple-700 text-xs"
                    >
                      Receive
                    </Link>

                    <!-- DELETE (Hanya RFQ) -->
                    <Link
                      v-if="order.status === 'RFQ'"
                      :href="`/purchase/${order.id}`"
                      method="delete"
                      as="button"
                      class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700 text-xs"
                    >
                      Delete
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
