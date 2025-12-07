<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  employees: Object, // expects paginated response: data, links, total, from, to, etc.
});
</script>

<template>
  <Head title="Employee" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Employee
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Daftar karyawan — master data. Tambah, edit, lihat detail, atau hapus karyawan.
          </p>
        </div>

        <div class="flex gap-2">
          <Link
            :href="route('employees.create')"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            aria-label="Create Employee"
          >
            + Create Employee
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <!-- TABLE -->
          <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
              <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                  <th class="p-3 border text-left">Kode</th>
                  <th class="p-3 border text-left">Nama</th>
                  <th class="p-3 border text-left">Jabatan</th>
                  <th class="p-3 border text-left">Hire Date</th>
                  <th class="p-3 border text-center">Status</th>
                  <th class="p-3 border text-center">Aksi</th>
                </tr>
              </thead>

              <tbody class="text-sm">
                <tr v-for="e in employees.data" :key="e.id" class="hover:bg-gray-50">
                  <td class="p-3 border font-mono text-blue-600 font-semibold">
                    <a :href="`/employees/${e.id}`" class="hover:underline">
                      {{ e.code || '-' }}
                    </a>
                  </td>

                  <td class="p-3 border font-medium text-gray-900">
                    {{ e.name }}
                  </td>

                  <td class="p-3 border text-gray-600">
                    {{ e.position || '-' }}
                  </td>

                  <td class="p-3 border">
                    {{ e.hire_date || '-' }}
                  </td>

                  <td class="p-3 border text-center">
                    <span
                      class="px-2 py-1 rounded text-xs font-bold"
                      :class="{
                        'bg-green-100 text-green-800': e.status === 'ACTIVE',
                        'bg-gray-100 text-gray-700': e.status !== 'ACTIVE'
                      }"
                    >
                      {{ e.status || '-' }}
                    </span>
                  </td>

                  <td class="p-3 border text-center space-x-1">
                    <Link
                      :href="route('employees.edit', e.id)"
                      class="text-blue-600 hover:underline text-xs ml-2"
                      aria-label="Edit employee"
                    >
                      Edit
                    </Link>

                    <!-- Delete as button (Inertia Link supports method/as) -->
                    <Link
                      :href="route('employees.destroy', e.id)"
                      method="delete"
                      as="button"
                      class="text-red-600 hover:underline text-xs ml-2"
                      aria-label="Delete employee"
                      @click="confirm('Hapus karyawan ini?')"
                    >
                      Delete
                    </Link>
                  </td>
                </tr>

                <tr v-if="employees.data.length === 0">
                  <td colspan="6" class="p-6 text-center text-gray-500">
                    Belum ada data karyawan.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- PAGINATION -->
          <div class="mt-4 flex justify-between items-center" v-if="employees.total > 0">
            <div class="text-sm text-gray-500">
              Menampilkan {{ employees.from }} sampai {{ employees.to }} dari {{ employees.total }} data.
            </div>

            <div class="flex gap-1">
              <Link
                v-for="(link, k) in employees.links"
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
