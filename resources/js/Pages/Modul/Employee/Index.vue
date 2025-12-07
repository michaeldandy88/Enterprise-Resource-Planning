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
          <h2 class="text-xl font-semibold">Employees</h2>
          <p class="text-sm text-gray-500">Master data karyawan.</p>
        </div>
        <div>
          <Link :href="route('employees.create')" class="rounded bg-blue-600 px-4 py-2 text-white">+ Create Employee</Link>
        </div>
      </div>
    </template>

    <div class="p-6">
      <div class="bg-white p-4 rounded shadow">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2">Code</th>
              <th>Name</th>
              <th>Position</th>
              <th>Email</th>
              <th>Status</th>
              <th class="w-32">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in employees.data" :key="e.id" class="border-b">
              <td class="py-2">{{ e.code || '-' }}</td>
              <td>{{ e.name }}</td>
              <td>{{ e.position || '-' }}</td>
              <td>{{ e.email || '-' }}</td>
              <td>{{ e.status }}</td>
              <td>
                <Link :href="route('employees.edit', e.id)" class="text-blue-600 text-xs">Edit</Link>
                <Link :href="route('employees.destroy', e.id)" method="delete" as="button" class="text-red-600 text-xs ml-2">Delete</Link>
              </td>
            </tr>
            <tr v-if="employees.data.length === 0">
              <td colspan="6" class="py-6 text-center text-gray-500">Belum ada data karyawan.</td>
            </tr>
          </tbody>
        </table>

        <div class="mt-4 flex justify-between items-center text-sm">
          <div>Page {{ employees.current_page }} / {{ employees.last_page }}</div>
          <div class="space-x-2">
            <button :disabled="!employees.prev_page_url" @click="$inertia.visit(employees.prev_page_url)" class="px-3 py-1 rounded bg-gray-200">Prev</button>
            <button :disabled="!employees.next_page_url" @click="$inertia.visit(employees.next_page_url)" class="px-3 py-1 rounded bg-gray-200">Next</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
