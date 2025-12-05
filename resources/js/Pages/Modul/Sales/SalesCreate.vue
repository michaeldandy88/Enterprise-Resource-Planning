<script setup>
import { ref, reactive, computed } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  customers: Array,
  products: Array,
});

// --- FORM STATE ---
const form = useForm({
  so_number: "",
  order_date: new Date().toISOString().split("T")[0],
  customer_id: "",
  items: [
    { product_id: "", qty: 1, price: 0, subtotal: 0 }
  ]
});

// --- HITUNG SUBTOTAL ---
function updateSubtotal(index) {
  const row = form.items[index];
  row.subtotal = row.qty * row.price;
}

// --- TAMBAH ROW ITEM ---
function addItem() {
  form.items.push({ product_id: "", qty: 1, price: 0, subtotal: 0 });
}

// --- HAPUS ROW ITEM ---
function removeItem(index) {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
}

// --- HITUNG TOTAL ---
const totalAmount = computed(() =>
  form.items.reduce((sum, item) => sum + item.subtotal, 0)
);

// --- SUBMIT FORM ---
function submitForm() {
  form.post("/sales/store");
}
</script>

<template>
  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">
        
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Buat Sales Order
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Form pembuatan pesanan penjualan pelanggan.
          </p>
        </div>

        <Link
          href="/sales"
          class="text-sm text-gray-600 hover:underline"
        >
          ← Kembali ke Sales
        </Link>

      </div>
    </template>

    <!-- BODY -->
    <div class="py-6">
      <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

          <!-- FORM HEADER -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
              <label class="block text-sm font-medium">SO Number</label>
              <input 
                v-model="form.so_number"
                type="text"
                class="mt-1 w-full rounded border-gray-300"
                placeholder="SO-2025-001"
              />
            </div>

            <div>
              <label class="block text-sm font-medium">Order Date</label>
              <input 
                v-model="form.order_date"
                type="date"
                class="mt-1 w-full rounded border-gray-300"
              />
            </div>

            <div>
              <label class="block text-sm font-medium">Customer</label>
              <select 
                v-model="form.customer_id"
                class="mt-1 w-full rounded border-gray-300"
              >
                <option value="">-- Pilih Customer --</option>
                <option v-for="c in props.customers" :key="c.id" :value="c.id">
                  {{ c.customer_code }} — {{ c.name }}
                </option>
              </select>
            </div>

          </div>

          <!-- ITEM TABLE -->
          <div>
            <h3 class="text-lg font-semibold mb-2">Items</h3>

            <table class="min-w-full border border-gray-300 rounded">
              <thead class="bg-gray-100 text-sm">
                <tr>
                  <th class="border p-2">Product</th>
                  <th class="border p-2 w-24">Qty</th>
                  <th class="border p-2 w-32">Price</th>
                  <th class="border p-2 w-32">Subtotal</th>
                  <th class="border p-2 w-16">Aksi</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(item, index) in form.items" :key="index" class="text-sm">
                  
                  <!-- PRODUCT -->
                  <td class="border p-2">
                    <select 
                      v-model="item.product_id"
                      class="w-full rounded border-gray-300"
                    >
                      <option value="">Pilih Produk</option>
                      <option v-for="p in props.products" :key="p.id" :value="p.id">
                        {{ p.code }} — {{ p.name }}
                      </option>
                    </select>
                  </td>

                  <!-- QTY -->
                  <td class="border p-2">
                    <input 
                      v-model.number="item.qty"
                      @input="updateSubtotal(index)"
                      type="number"
                      class="w-full rounded border-gray-300"
                      min="1"
                    />
                  </td>

                  <!-- PRICE -->
                  <td class="border p-2">
                    <input 
                      v-model.number="item.price"
                      @input="updateSubtotal(index)"
                      type="number"
                      class="w-full rounded border-gray-300"
                      min="0"
                    />
                  </td>

                  <!-- SUBTOTAL -->
                  <td class="border p-2">
                    Rp {{ item.subtotal }}
                  </td>

                  <!-- DELETE -->
                  <td class="border p-2 text-center">
                    <button
                      @click="removeItem(index)"
                      class="text-red-600 hover:underline"
                    >
                      Hapus
                    </button>
                  </td>

                </tr>
              </tbody>
            </table>

            <!-- ADD ITEM BUTTON -->
            <button
              @click="addItem"
              class="mt-3 rounded bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700"
            >
              + Tambah Item
            </button>
          </div>

          <!-- TOTAL -->
          <div class="text-right text-lg font-semibold">
            Total: Rp {{ totalAmount }}
          </div>

          <!-- SUBMIT -->
          <button
            @click="submitForm"
            class="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700"
          >
            Simpan Sales Order
          </button>

        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>
