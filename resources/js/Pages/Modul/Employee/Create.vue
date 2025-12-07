<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  code: '',
  name: '',
  position: '',
  email: '',
  phone: '',
  hire_date: '',
  status: 'ACTIVE',
  notes: '',
});

const submit = () => form.post(route('employees.store'));
</script>

<template>
  <Head title="Create Employee" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Create Employee</h2>
        <Link :href="route('employees.index')" class="text-sm text-gray-600">← Back</Link>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-lg bg-white p-6 rounded shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- fields: code, name, position, email, phone, hire_date, status, notes -->
          <div>
            <label class="block text-sm">Code</label>
            <input v-model="form.code" type="text" class="w-full rounded border-gray-300" />
            <div v-if="form.errors.code" class="text-xs text-red-600">{{ form.errors.code }}</div>
          </div>

          <div>
            <label class="block text-sm">Name</label>
            <input v-model="form.name" type="text" class="w-full rounded border-gray-300" />
            <div v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</div>
          </div>

          <div>
            <label class="block text-sm">Position</label>
            <input v-model="form.position" type="text" class="w-full rounded border-gray-300" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm">Email</label>
              <input v-model="form.email" type="email" class="w-full rounded border-gray-300" />
            </div>
            <div>
              <label class="block text-sm">Phone</label>
              <input v-model="form.phone" type="text" class="w-full rounded border-gray-300" />
            </div>
          </div>

          <div>
            <label class="block text-sm">Hire Date</label>
            <input v-model="form.hire_date" type="date" class="w-full rounded border-gray-300" />
          </div>

          <div>
            <label class="block text-sm">Status</label>
            <select v-model="form.status" class="w-full rounded border-gray-300">
              <option value="ACTIVE">ACTIVE</option>
              <option value="INACTIVE">INACTIVE</option>
            </select>
          </div>

          <div>
            <label class="block text-sm">Notes</label>
            <textarea v-model="form.notes" class="w-full rounded border-gray-300"></textarea>
          </div>

          <div class="flex justify-between items-center">
            <Link :href="route('employees.index')" class="text-sm text-gray-600">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="rounded bg-blue-600 px-4 py-2 text-white">Save</button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
