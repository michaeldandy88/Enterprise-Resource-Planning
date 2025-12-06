<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  customers: Object
});
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Customers
        </h2>
        <Link
          :href="route('customers.create')"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
        >
          + Tambah Customer
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
          <div v-if="customers.data.length === 0" class="text-gray-600">
            Belum ada data customer.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
              <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                  <th class="p-3 border">Kode</th>
                  <th class="p-3 border">Nama</th>
                  <th class="p-3 border">Email</th>
                  <th class="p-3 border">Telepon</th>
                  <th class="p-3 border">Kota</th>
                  <th class="p-3 border text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="text-sm">
                <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-gray-50">
                  <td class="p-3 border font-medium">{{ customer.customer_code }}</td>
                  <td class="p-3 border">{{ customer.name }}</td>
                  <td class="p-3 border">{{ customer.email }}</td>
                  <td class="p-3 border">{{ customer.phone }}</td>
                  <td class="p-3 border">{{ customer.city }}</td>
                  <td class="p-3 border text-center space-x-2">
                    <Link
                      :href="route('customers.edit', customer.id)"
                      class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600 text-xs"
                    >
                      Edit
                    </Link>
                    <Link
                      method="delete"
                      :href="route('customers.destroy', customer.id)"
                      as="button"
                      :onBefore="() => confirm('Yakin ingin menghapus customer ini?')"
                      class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700 text-xs"
                    >
                      Hapus
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4 flex justify-between items-center" v-if="customers.total > customers.per_page">
             <div class="text-sm text-gray-500">
                Menampilkan {{ customers.from }} sampai {{ customers.to }} dari {{ customers.total }} data.
            </div>
            <div class="flex gap-1">
                <Link
                    v-for="(link, k) in customers.links"
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
