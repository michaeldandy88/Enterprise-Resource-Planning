<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
  bom: Object,
  raw_products: Array,
});

// form update BOM
const formBom = useForm({
  name: props.bom.name,
  is_active: props.bom.is_active,
});

// form tambah item
const formItem = useForm({
  product_id: "",
  qty_per_unit: "",
});

// submit update BOM
const updateBom = () => {
  formBom.patch(route("boms.update", props.bom.id));
};

// submit tambah item BOM
const addItem = () => {
  formItem.post(route("boms.items.store", props.bom.id), {
    onSuccess: () => {
      formItem.reset();
    },
  });
};
</script>

<template>
  <Head :title="`Edit BOM: ${bom.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">
            Edit BOM — {{ bom.name }}
          </h2>
          <p class="text-sm text-gray-500">
            Atur komposisi bahan baku untuk produk jadi.
          </p>
        </div>

        <Link
          :href="route('manufacturing')"
          class="text-sm text-gray-600 hover:underline"
        >
          ← Kembali ke Manufacturing
        </Link>
      </div>
    </template>

    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- =======================
           FORM UPDATE BOM
      ======================== -->
      <div class="rounded-lg bg-white p-6 shadow">
        <h3 class="font-semibold mb-4 text-gray-800">Informasi BOM</h3>

        <form @submit.prevent="updateBom" class="space-y-4">
          <!-- BOM Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Nama BOM
            </label>
            <input
              v-model="formBom.name"
              class="w-full rounded border-gray-300 text-sm"
              type="text"
            />
            <div
              v-if="formBom.errors.name"
              class="mt-1 text-xs text-red-600"
            >
              {{ formBom.errors.name }}
            </div>
          </div>

          <!-- Active -->
          <div class="flex items-center gap-2">
            <input
              id="is_active"
              type="checkbox"
              v-model="formBom.is_active"
              class="rounded border-gray-300"
            />
            <label class="text-sm" for="is_active">Aktif</label>
          </div>

          <button
            type="submit"
            class="rounded bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700"
          >
            Simpan Perubahan
          </button>
        </form>
      </div>

      <!-- =======================
           FORM TAMBAH ITEM
      ======================== -->
      <div class="rounded-lg bg-white p-6 shadow">
        <h3 class="font-semibold mb-4 text-gray-800">Tambah Item BOM</h3>

        <form @submit.prevent="addItem" class="space-y-4">
          <!-- Raw Product -->
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Bahan Baku
            </label>
            <select
              v-model="formItem.product_id"
              class="w-full rounded border-gray-300 text-sm"
            >
              <option value="">-- Pilih Bahan Baku --</option>
              <option
                v-for="raw in raw_products"
                :key="raw.id"
                :value="raw.id"
              >
                {{ raw.code }} - {{ raw.name }}
              </option>
            </select>
            <div
              v-if="formItem.errors.product_id"
              class="mt-1 text-xs text-red-600"
            >
              {{ formItem.errors.product_id }}
            </div>
          </div>

          <!-- Qty per unit -->
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Qty per Unit
            </label>
            <input
              type="number"
              step="0.0001"
              v-model="formItem.qty_per_unit"
              class="w-full rounded border-gray-300 text-sm"
            />
            <div
              v-if="formItem.errors.qty_per_unit"
              class="mt-1 text-xs text-red-600"
            >
              {{ formItem.errors.qty_per_unit }}
            </div>
          </div>

          <button
            type="submit"
            class="rounded bg-green-600 text-white px-4 py-2 text-sm font-semibold hover:bg-green-700"
          >
            Tambah Item
          </button>
        </form>
      </div>
    </div>

    <!-- ==============================
         TABEL LIST ITEM BOM
    ============================== -->
    <div class="p-6">
      <div class="rounded-lg bg-white p-6 shadow">
        <h3 class="font-semibold mb-4 text-gray-800">Daftar Item BOM</h3>

        <table class="w-full border-collapse text-left text-sm">
          <thead>
            <tr class="border-b font-semibold text-gray-700">
              <th class="py-2">Bahan Baku</th>
              <th>Qty per Unit</th>
              <th>Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="item in bom.items"
              :key="item.id"
              class="border-b last:border-b-0"
            >
              <td>{{ item.product?.name }}</td>
              <td>{{ item.qty_per_unit }}</td>
              <td>
                <Link
                  :href="route('boms.items.destroy', [bom.id, item.id])"
                  method="delete"
                  as="button"
                  class="text-red-600 hover:underline text-sm"
                >
                  Hapus
                </Link>
              </td>
            </tr>

            <tr v-if="bom.items.length === 0">
              <td colspan="3" class="text-center py-4 text-gray-500">
                Belum ada item pada BOM ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
