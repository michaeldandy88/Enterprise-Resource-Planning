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

                    <td class="p-3 border text-center space-x-2">
                      <Link
                        v-if="order.status === 'approved'"
                        :href="route('sales.create_invoice', order.id)"
                        method="post"
                        as="button"
                        class="rounded bg-purple-600 px-3 py-1 text-white hover:bg-purple-700 text-xs inline-block"
                      >
                        Create Invoice
                      </Link>

                      <a
                        v-if="order.status === 'approved'"
                        :href="route('sales.print', order.id)"
                        target="_blank"
                        class="rounded bg-gray-600 px-3 py-1 text-white hover:bg-gray-700 text-xs inline-block"
                      >
                        Cetak
                      </a>

                      <Link
                        :href="route('sales.edit', order.id)"
                        class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600 text-xs"
                      >
                        Edit
                      </Link>

                      <Link
                        method="delete"
                        :href="route('sales.delete', order.id)"
                        as="button"
                        :onBefore="() => confirm('Apakah Anda yakin ingin menghapus SO ini? Item yang sudah terjual tidak akan kembali ke stok secara otomatis (perlu penyesuaian manual).')"
                        class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700 text-xs"
                      >
                        Hapus
                      </Link>
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
