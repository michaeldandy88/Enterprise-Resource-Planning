<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  invoices: Object
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Invoices
        </h2>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
          <div v-if="invoices.data.length === 0" class="text-gray-600">
            Belum ada invoice.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
              <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                  <th class="p-3 border">Nomor Invoice</th>
                  <th class="p-3 border">Tanggal</th>
                  <th class="p-3 border">Customer</th>
                  <th class="p-3 border">Total</th>
                  <th class="p-3 border">Status</th>
                  <th class="p-3 border text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="text-sm">
                <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50">
                  <td class="p-3 border font-medium">{{ inv.invoice_number }}</td>
                  <td class="p-3 border">{{ formatDate(inv.invoice_date) }}</td>
                  <td class="p-3 border">{{ inv.customer?.name }}</td>
                  <td class="p-3 border text-right">{{ formatCurrency(inv.total_amount) }}</td>
                  <td class="p-3 border text-center">
                    <span :class="{
                        'bg-gray-100 text-gray-800': inv.status === 'draft',
                        'bg-blue-100 text-blue-800': inv.status === 'posted',
                        'bg-green-100 text-green-800': inv.status === 'paid',
                    }" class="px-2 py-1 rounded text-xs font-semibold uppercase">
                        {{ inv.status }}
                    </span>
                  </td>
                  <td class="p-3 border text-center">
                    <Link
                      :href="route('invoices.show', inv.id)"
                      class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700 text-xs"
                    >
                      Detail
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4 flex justify-between items-center" v-if="invoices.total > invoices.per_page">
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
