<script setup>
import { useForm, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const form = useForm({
  customer_code: "",
  name: "",
  email: "",
  phone: "",
  address: "",
  city: "",
  country: "Indonesia",
});

function submit() {
  form.post(route("customers.store"));
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Tambah Customer
        </h2>
        <Link :href="route('customers.index')" class="text-sm text-gray-600 hover:underline">
          ← Kembali
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-4">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium">Kode Customer</label>
                <input v-model="form.customer_code" type="text" class="mt-1 w-full rounded border-gray-300" placeholder="CUST001" />
                <div v-if="form.errors.customer_code" class="text-red-500 text-xs mt-1">{{ form.errors.customer_code }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium">Nama Perusahaan / Perorangan</label>
                <input v-model="form.name" type="text" class="mt-1 w-full rounded border-gray-300" />
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" class="mt-1 w-full rounded border-gray-300" />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium">Telepon</label>
                <input v-model="form.phone" type="text" class="mt-1 w-full rounded border-gray-300" />
                <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium">Alamat Lengkap</label>
              <textarea v-model="form.address" rows="3" class="mt-1 w-full rounded border-gray-300"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium">Kota</label>
                <input v-model="form.city" type="text" class="mt-1 w-full rounded border-gray-300" />
              </div>

              <div>
                <label class="block text-sm font-medium">Negara</label>
                <input v-model="form.country" type="text" class="mt-1 w-full rounded border-gray-300" />
              </div>
            </div>

            <div class="flex justify-end pt-4">
              <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
              >
                Simpan Customer
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
