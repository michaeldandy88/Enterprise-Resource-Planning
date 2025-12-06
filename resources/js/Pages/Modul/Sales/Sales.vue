<script setup>
import { Head,Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  orders: Object, // Changed from Array to Object for pagination
  currentType: String // 'quotation' or 'order' or null
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const pageTitle = props.currentType === 'quotation' ? 'Quotations' : (props.currentType === 'order' ? 'Sales Orders' : 'All Sales Documents');
</script>

<template>
  <Head :title="pageTitle" />
    <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">

        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ pageTitle }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
             {{ props.currentType === 'quotation' ? 'Daftar penawaran harga (Draft/Submitted).' : (props.currentType === 'order' ? 'Daftar pesanan penjualan yang valid (Approved).' : 'Semua dokumen penjualan.') }}
          </p>
        </div>

        <div class="flex gap-2">
            <!-- Filter Tabs -->
            <div class="flex bg-gray-200 rounded p-1 mr-4">
                <Link
                    :href="route('sales.index', { type: 'quotation' })"
                    class="px-4 py-2 text-sm font-medium rounded"
                    :class="currentType === 'quotation' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800'"
                >
                    Quotations
                </Link>
                <Link
                    :href="route('sales.index', { type: 'order' })"
                    class="px-4 py-2 text-sm font-medium rounded"
                    :class="currentType === 'order' ? 'bg-white text-gray-800 shadow' : 'text-gray-600 hover:text-gray-800'"
                >
                    Orders
                </Link>
            </div>

            <Link
                :href="route('customers.index')"
                class="rounded bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
            >
                Customers
            </Link>
            <Link
                :href="route('invoices.index')"
                class="rounded bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700"
            >
                Invoices
            </Link>
            <Link
                :href="route('sales.create')"
                class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            >
                + Buat Sales Order
            </Link>
        </div>

      </div>
    </template>

    <!-- KONTEN -->
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">

          <!-- Jika tidak ada data -->
          <div v-if="orders.data.length === 0" class="text-gray-600">
            Belum ada Sales Order.
          </div>

          <!-- TABEL -->
          <div v-else>
            <div class="overflow-x-auto">
              <table class="min-w-full border border-gray-200 rounded">
                <thead class="bg-gray-100 text-sm text-gray-700">
                  <tr>
                    <th class="p-3 border">Kode SO</th>
                    <th class="p-3 border">Tanggal</th>
                    <th class="p-3 border">Customer</th>
                    <th class="p-3 border">Total</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border text-center">Aksi</th>
                  </tr>
                </thead>

                <tbody class="text-sm">
                  <tr
                    v-for="order in orders.data"
                    :key="order.id"
                    class="hover:bg-gray-50"
                  >
                    <td class="p-3 border font-medium">{{ order.so_number }}</td>
                    <td class="p-3 border">{{ order.order_date }}</td>
                    <td class="p-3 border">{{ order.customer?.name }}</td>
                    <td class="p-3 border">{{ formatCurrency(order.total_amount) }}</td>
                    <td class="p-3 border">
                        <span :class="{
                            'bg-gray-100 text-gray-800': order.status === 'draft',
                            'bg-blue-100 text-blue-800': order.status === 'submitted',
                            'bg-green-100 text-green-800': order.status === 'approved',
                            'bg-red-100 text-red-800': order.status === 'rejected',
                        }" class="px-2 py-1 rounded text-xs font-semibold">
                            {{ order.status }}
                        </span>
                    </td>

                    <td class="p-3 border text-center">
                      <div class="flex justify-center items-center gap-2">
                        
                        <!-- CREATE INVOICE (Approved Only) -->
                        <Link
                          v-if="order.status === 'approved'"
                          :href="route('sales.create_invoice', order.id)"
                          method="post"
                          as="button"
                          class="text-purple-600 hover:text-purple-800 transition-colors p-1"
                          title="Buat Invoice"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0011.378 2H4.5zm2.25 8.5a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 3a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                          </svg>
                        </Link>

                        <!-- PRINT (Approved Only) -->
                        <a
                          v-if="order.status === 'approved'"
                          :href="route('sales.print', order.id)"
                          target="_blank"
                          class="text-gray-500 hover:text-gray-800 transition-colors p-1"
                          title="Cetak Sales Order"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.5A1.75 1.75 0 0113.25 8H6.75A1.75 1.75 0 015 6.25v-3.5zm1.75-.25a.25.25 0 00-.25.25v3.5c0 .138.112.25.25.25h6.5a.25.25 0 00.25-.25v-3.5a.25.25 0 00-.25-.25h-6.5z" clip-rule="evenodd" />
                            <path d="M3.5 9A1.5 1.5 0 002 10.5v4A1.5 1.5 0 003.5 16h.75a.75.75 0 001.5 0v-1.5h8.5V16a.75.75 0 001.5 0h.75a1.5 1.5 0 001.5-1.5v-4a1.5 1.5 0 00-1.5-1.5h-14.5z" />
                            <path d="M6.75 19a.75.75 0 000-1.5h6.5a.75.75 0 000 1.5h-6.5z" />
                          </svg>
                        </a>

                        <!-- EDIT -->
                        <Link
                          :href="route('sales.edit', order.id)"
                          class="text-blue-600 hover:text-blue-800 transition-colors p-1"
                          title="Edit Sales Order"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                          </svg>
                        </Link>

                        <!-- DELETE -->
                        <Link
                          method="delete"
                          :href="route('sales.delete', order.id)"
                          as="button"
                          :onBefore="() => confirm('Apakah Anda yakin ingin menghapus SO ini?')"
                          class="text-red-600 hover:text-red-800 transition-colors p-1"
                          title="Hapus Sales Order"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                          </svg>
                        </Link>
                      </div>
                    </td>

                  </tr>

                </tbody>
              </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-4 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ orders.from }} sampai {{ orders.to }} dari {{ orders.total }} data.
                </div>
                <div class="flex gap-1">
                    <Link
                        v-for="(link, k) in orders.links"
                        :key="k"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 border rounded text-sm"
                        :class="{
                            'bg-blue-600 text-white border-blue-600': link.active,
                            'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                            'opacity-50 cursor-not-allowed': !link.url
                        }"
                    />
                </div>
            </div>
          </div>

        </div>

      </div>
    </div>

  </AuthenticatedLayout>
</template>
