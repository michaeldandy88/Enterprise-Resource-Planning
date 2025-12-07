<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  employees: Object,
});
</script>

<template>
  <Head title="Employees" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">Employees</h2>
          <p class="mt-1 text-sm text-gray-500">Master data karyawan.</p>
        </div>

        <div class="flex gap-2">
          <Link :href="route('employees.create')" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Create Employee</Link>
          <Link :href="route('manufacturing')" class="text-sm text-gray-600 hover:underline">← Back</Link>
        </div>
      </div>
    </template>

    <div class="p-6">
      <div class="rounded bg-white p-6 shadow">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2">Kode</th>
              <th>Nama</th>
              <th>Posisi</th>
              <th>Status</th>
              <th class="w-32">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in employees.data" :key="e.id" class="border-b">
              <td class="py-2 font-mono text-xs">{{ e.code || '-' }}</td>
              <td>{{ e.name }}</td>
              <td>{{ e.position || '-' }}</td>
              <td>{{ e.status }}</td>
              <td class="space-x-2">
                <Link
                    :href="route('employees.show', e.id)"
                    aria-label="Lihat detail karyawan {{ e.name }}"
                    class="text-blue-600 text-xs hover:underline"
                >
                    Detail
                </Link>
                <Link :href="route('employees.edit', e.id)" class="text-blue-600 text-xs hover:underline">Edit</Link>
                <Link :href="route('employees.destroy', e.id)" method="delete" as="button" class="text-red-600 text-xs hover:underline">Delete</Link>
              </td>
            </tr>
            <tr v-if="employees.data.length === 0">
              <td colspan="5" class="py-6 text-center text-gray-500">Belum ada karyawan.</td>
            </tr>
          </tbody>
        </table>

        <div class="mt-4 flex justify-between items-center text-sm">
          <div>Halaman {{ employees.current_page }} / {{ employees.last_page }}</div>
          <div class="space-x-2">
            <button :disabled="!employees.prev_page_url" @click="$inertia.visit(employees.prev_page_url)" class="px-3 py-1 rounded bg-gray-200">Prev</button>
            <button :disabled="!employees.next_page_url" @click="$inertia.visit(employees.next_page_url)" class="px-3 py-1 rounded bg-gray-200">Next</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
