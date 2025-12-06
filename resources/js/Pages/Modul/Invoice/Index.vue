<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  invoices: Object,
  currentType: String
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const pageTitle = props.currentType === 'PURCHASE' ? 'Vendor Bills (Tagihan Pembelian)' : 'Customer Invoices (Tagihan Penjualan)';
</script>

<template>
  <Head :title="pageTitle" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle }}</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- TABS -->
        <div class="flex space-x-4 mb-4 border-b border-gray-200">
            <Link
                :href="route('invoices.index', { type: 'SALES' })"
                class="pb-2 px-1 text-sm font-medium border-b-2 transition-colors duration-200"
                :class="currentType === 'SALES' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            >
                Customer Invoices
            </Link>
            <Link
                :href="route('invoices.index', { type: 'PURCHASE' })"
                class="pb-2 px-1 text-sm font-medium border-b-2 transition-colors duration-200"
                :class="currentType === 'PURCHASE' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            >
                Vendor Bills
            </Link>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            
            <table class="min-w-full border-collapse border border-gray-200">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border p-2 text-left">No. Invoice</th>
                  <th class="border p-2 text-left">Tanggal</th>
                  <th class="border p-2 text-left">
                      {{ currentType === 'PURCHASE' ? 'Supplier' : 'Customer' }}
                  </th>
                  <th class="border p-2 text-right">Total</th>
                  <th class="border p-2 text-center">Status</th>
                  <th class="border p-2 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50">
                  <td class="border p-2 font-mono text-blue-600">{{ inv.invoice_number }}</td>
                  <td class="border p-2">{{ inv.invoice_date }}</td>
                  <td class="border p-2 font-medium">
                      {{ currentType === 'PURCHASE' ? inv.supplier?.name : inv.customer?.name }}
                  </td>
                  <td class="border p-2 text-right">{{ formatCurrency(inv.total_amount) }}</td>
                  <td class="border p-2 text-center">
                    <span :class="{
                        'bg-gray-200 text-gray-800': inv.status === 'draft',
                        'bg-blue-100 text-blue-800': inv.status === 'posted',
                        'bg-green-100 text-green-800': inv.status === 'paid',
                    }" class="px-2 py-1 rounded text-xs font-bold uppercase">
                        {{ inv.status }}
                    </span>
                  </td>
                  <td class="border p-2 text-center">
                    <Link :href="route('invoices.show', inv.id)" class="text-blue-600 hover:underline text-sm font-semibold">
                        Lihat Detail
                    </Link>
                  </td>
                </tr>
                <tr v-if="invoices.data.length === 0">
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        Belum ada data {{ currentType === 'PURCHASE' ? 'tagihan pembelian' : 'invoice penjualan' }}.
                    </td>
                </tr>
              </tbody>
            </table>
             <div class="text-sm text-gray-500">
                Menampilkan {{ invoices.from }} sampai {{ invoices.to }} dari {{ invoices.total }} data.
            </div>
            <div class="flex gap-1">
                <Link
                    v-for="(link, k) in invoices.links"
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
  </AuthenticatedLayout>
</template>
