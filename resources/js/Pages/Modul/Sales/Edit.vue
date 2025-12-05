<script setup>
import { ref, computed } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  order: Object,
  customers: Array,
  products: Array,
});

// --- FORM STATE ---
// Inisialisasi form dengan data yang ada
const form = useForm({
  so_number: props.order.so_number,
  order_date: props.order.order_date,
  customer_id: props.order.customer_id,
  status: props.order.status,
  items: props.order.items.map(item => ({
    product_id: item.product_id,
    qty: parseFloat(item.qty),
    price: parseFloat(item.price),
    subtotal: parseFloat(item.subtotal)
  }))
});

// Jika items kosong (misal data rusak), kasih 1 row kosong
if (form.items.length === 0) {
  form.items.push({ product_id: "", qty: 1, price: 0, subtotal: 0 });
}

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
  form.post(route('sales.update', props.order.id), {
    onSuccess: () => {
        // Optional: show toast
    }
  });
}

// Helper untuk format currency
const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">
        
        <div>
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Sales Order: {{ order.so_number }}
          </h2>
        </div>

        <Link
          :href="route('sales.index')"
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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
              <label class="block text-sm font-medium">SO Number</label>
              <input 
                v-model="form.so_number"
                type="text"
                class="mt-1 w-full rounded border-gray-300 bg-gray-100"
                readonly
              />
            </div>

            <div>
              <label class="block text-sm font-medium">Order Date</label>
              <input 
                v-model="form.order_date"
                type="date"
                class="mt-1 w-full rounded border-gray-300"
              />
              <div v-if="form.errors.order_date" class="text-red-500 text-xs mt-1">{{ form.errors.order_date }}</div>
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
              <div v-if="form.errors.customer_id" class="text-red-500 text-xs mt-1">{{ form.errors.customer_id }}</div>
            </div>

             <div>
              <label class="block text-sm font-medium">Status</label>
              <select 
                v-model="form.status"
                class="mt-1 w-full rounded border-gray-300"
              >
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

          </div>

          <!-- ITEM TABLE -->
          <div>
            <h3 class="text-lg font-semibold mb-2">Items</h3>
            <div v-if="form.errors['product_id']" class="text-red-500 text-xs mb-2">{{ form.errors['product_id'] }}</div>

            <table class="min-w-full border border-gray-300 rounded">
              <thead class="bg-gray-100 text-sm">
                <tr>
                  <th class="border p-2">Product</th>
                  <th class="border p-2 w-24">Qty</th>
                  <th class="border p-2 w-32">Price</th>
                  <th class="border p-2 w-40">Subtotal</th>
                  <th class="border p-2 w-16">Aksi</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(item, index) in form.items" :key="index" class="text-sm">
                  
                  <!-- PRODUCT -->
                  <td class="border p-2">
                    <select 
                      v-model="item.product_id"
                      class="w-full rounded border-gray-300 text-sm"
                    >
                      <option value="">Pilih Produk</option>
                      <option v-for="p in props.products" :key="p.id" :value="p.id">
                        {{ p.code }} — {{ p.name }} (Stok: {{ p.current_stock }} {{ p.uom }})
                      </option>
                    </select>
                    <div v-if="form.errors[`items.${index}.product_id`]" class="text-red-500 text-xs mt-1">Produk wajib dipilih</div>
                  </td>

                  <!-- QTY -->
                  <td class="border p-2">
                    <input 
                      v-model.number="item.qty"
                      @input="updateSubtotal(index)"
                      type="number"
                      class="w-full rounded border-gray-300 text-sm"
                      min="1"
                    />
                    <div v-if="form.errors[`items.${index}.qty`]" class="text-red-500 text-xs mt-1">Min 1</div>
                  </td>

                  <!-- PRICE -->
                  <td class="border p-2">
                    <input 
                      v-model.number="item.price"
                      @input="updateSubtotal(index)"
                      type="number"
                      class="w-full rounded border-gray-300 text-sm"
                      min="0"
                    />
                    <div v-if="form.errors[`items.${index}.price`]" class="text-red-500 text-xs mt-1">Invalid</div>
                  </td>

                  <!-- SUBTOTAL -->
                  <td class="border p-2 text-right font-medium">
                    {{ formatCurrency(item.subtotal) }}
                  </td>

                  <!-- DELETE -->
                  <td class="border p-2 text-center">
                    <button
                      type="button"
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
              type="button"
              @click="addItem"
              class="mt-3 rounded bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700"
            >
              + Tambah Item
            </button>
          </div>

          <!-- TOTAL -->
          <div class="text-right text-xl font-bold text-gray-800">
            Total: {{ formatCurrency(totalAmount) }}
          </div>

          <!-- SUBMIT -->
          <div class="flex justify-end gap-3">
             <Link
                :href="route('sales.index')"
                class="rounded border border-gray-300 bg-white px-6 py-2 text-gray-700 hover:bg-gray-50"
              >
                Batal
              </Link>
            <button
              @click="submitForm"
              :disabled="form.processing"
              class="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
            >
              {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>

        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>
