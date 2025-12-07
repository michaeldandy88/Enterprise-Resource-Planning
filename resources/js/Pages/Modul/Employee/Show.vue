<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  employee: Object,
});

const fullName = computed(() => props.employee?.name ?? '-');
const initials = computed(() => {
  if (!props.employee?.name) return '';
  return props.employee.name
    .split(' ')
    .map(n => n[0])
    .slice(0,2)
    .join('')
    .toUpperCase();
});

const createdAt = computed(() => props.employee?.created_at ?? null);
const hireDate = computed(() => props.employee?.hire_date ?? null);
</script>

<template>
  <Head :title="`Employee — ${fullName}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">Employee — {{ fullName }}</h2>
          <p class="mt-1 text-sm text-gray-500">Detail profil karyawan.</p>
        </div>

        <div class="flex gap-2">
          <Link
            :href="route('employees.edit', employee.id)"
            class="rounded bg-yellow-500 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-600"
          >
            Edit
          </Link>

          <Link
            :href="route('employees.index')"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
          >
            Back to list
          </Link>
        </div>
      </div>
    </template>

    <div class="p-6">
      <div class="mx-auto max-w-4xl rounded-lg bg-white p-6 shadow">
        <div class="flex gap-6">
          <!-- Avatar / Photo -->
          <div class="w-28 h-28 shrink-0">
            <div v-if="employee.photo" class="w-28 h-28 rounded-full overflow-hidden bg-gray-100">
              <img :src="employee.photo" alt="photo" class="w-full h-full object-cover" />
            </div>

            <div v-else class="w-28 h-28 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
              {{ initials }}
            </div>
          </div>

          <!-- Basic info -->
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-lg font-semibold text-gray-900">{{ employee.name }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ employee.position || '-' }}</div>
              </div>

              <div class="text-right text-sm text-gray-500">
                <div>Code</div>
                <div class="font-mono text-gray-700 mt-1">{{ employee.code || '-' }}</div>
              </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-gray-700">
              <div>
                <div class="text-xs text-gray-500">Email</div>
                <div class="mt-1">{{ employee.email || '-' }}</div>
              </div>

              <div>
                <div class="text-xs text-gray-500">Phone</div>
                <div class="mt-1">{{ employee.phone || '-' }}</div>
              </div>

              <div>
                <div class="text-xs text-gray-500">Hire Date</div>
                <div class="mt-1">{{ hireDate || '-' }}</div>
              </div>

              <div>
                <div class="text-xs text-gray-500">Status</div>
                <div class="mt-1">
                  <span
                    :class="employee.status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700'"
                    class="inline-block px-2 py-1 text-xs rounded font-semibold"
                  >
                    {{ employee.status || '-' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes / Additional info -->
        <div class="mt-6">
          <h4 class="text-sm font-semibold text-gray-700 mb-2">Notes</h4>
          <div class="text-sm text-gray-700">
            <div v-if="employee.notes">
              <p v-html="employee.notes"></p>
            </div>
            <div v-else class="text-gray-400">Tidak ada catatan.</div>
          </div>
        </div>

        <!-- Meta -->
        <div class="mt-6 border-t pt-4 text-xs text-gray-500">
          <div>Dibuat: {{ createdAt || '-' }}</div>
          <div v-if="employee.deleted_at" class="text-red-600 mt-1">This employee is deleted (soft deleted).</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>