<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  boms: Object,
});
</script>

<template>
  <Head title="BOM List" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Bill of Materials (BOM)
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Daftar BOM untuk produk jadi dalam modul Manufacturing.
          </p>
        </div>

        <div class="flex gap-2">
          <Link
            :href="route('boms.create')"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
          >
            + Create BOM
          </Link>

          <Link
            :href="route('manufacturing')"
            class="text-sm text-gray-600 hover:underline"
          >
            ← Kembali ke Manufacturing
          </Link>
        </div>
      </div>
    </template>

    <div class="p-6">
      <div class="rounded-lg bg-white p-6 shadow">
        <table class="w-full border-collapse text-left text-sm">
          <thead>
            <tr class="border-b font-semibold text-gray-700">
              <th class="py-2">Produk Jadi</th>
              <th>Nama BOM</th>
              <th>Aktif</th>
              <th class="w-32">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="bom in boms.data"
              :key="bom.id"
              class="border-b last:border-0"
            >
              <td class="py-2">
                <div class="font-medium">
                  {{ bom.product?.name || "-" }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ bom.product?.code || "" }}
                </div>
              </td>
              <td>{{ bom.name }}</td>
              <td>
                <span
                  :class="[
                    'rounded px-2 py-0.5 text-xs font-semibold',
                    bom.is_active
                      ? 'bg-green-100 text-green-700'
                      : 'bg-gray-200 text-gray-600',
                  ]"
                >
                  {{ bom.is_active ? "Aktif" : "Nonaktif" }}
                </span>
              </td>
              <td class="space-x-2">
                <Link
                  :href="route('boms.edit', bom.id)"
                  class="text-blue-600 hover:underline text-xs"
                >
                  Edit
                </Link>

                <Link
                  :href="route('boms.destroy', bom.id)"
                  method="delete"
                  as="button"
                  class="text-red-600 hover:underline text-xs"
                >
                  Delete
                </Link>
              </td>
            </tr>

            <tr v-if="boms.data.length === 0">
              <td colspan="4" class="py-4 text-center text-gray-500">
                Belum ada BOM.
              </td>
            </tr>
          </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="mt-4 flex items-center justify-between text-sm">
          <div>
            Halaman {{ boms.current_page }} dari {{ boms.last_page }}
          </div>

          <div class="space-x-2">
            <button
              :disabled="!boms.prev_page_url"
              @click="$inertia.visit(boms.prev_page_url)"
              class="rounded bg-gray-200 px-3 py-1 disabled:cursor-not-allowed disabled:opacity-40"
            >
              Prev
            </button>

            <button
              :disabled="!boms.next_page_url"
              @click="$inertia.visit(boms.next_page_url)"
              class="rounded bg-gray-200 px-3 py-1 disabled:cursor-not-allowed disabled:opacity-40"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
