<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    suppliers: Array,
    products: Array
});

// Form inertia
const form = useForm({
    po_number: "",
    supplier_id: "",
    order_date: new Date().toISOString().substr(0, 10),
    items: [
        { product_id: "", qty: 1, unit_price: 0 }
    ],
});

// Tambah baris item
function addItem() {
    form.items.push({ product_id: "", qty: 1, unit_price: 0 });
}

// Hapus item
function removeItem(index) {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
}

// Auto-fill price when product selected
function onProductChange(item) {
    const product = props.products.find(p => p.id === item.product_id);
    if (product) {
        item.unit_price = product.cost || 0;
    } else {
        item.unit_price = 0;
    }
}

// Hitung subtotal
function subtotal(item) {
    return item.qty * item.unit_price;
}

// Hitung total
function total() {
    return form.items.reduce((sum, i) => sum + subtotal(i), 0);
}

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};

const submit = () => {
    form.post(route('purchase.store'));
};
</script>


<template>
    <Head title="Buat Purchase Order" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Buat Purchase Order
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Buat permintaan penawaran (RFQ) baru ke vendor.
                    </p>
                </div>
                <Link
                    :href="route('purchase.index')"
                    class="text-sm text-gray-600 hover:underline"
                >
                    Lihat daftar PO
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <!-- FORM -->
                    <form @submit.prevent="submit" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- PO NUMBER -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor PO</label>
                                <input v-model="form.po_number" type="text" class="w-full rounded border-gray-300 text-sm" placeholder="Contoh: PO-2023-001" required />
                                <div v-if="form.errors.po_number" class="mt-1 text-xs text-red-600">{{ form.errors.po_number }}</div>
                            </div>

                            <!-- SUPPLIER -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor / Supplier</label>
                                <select v-model="form.supplier_id" class="w-full rounded border-gray-300 text-sm" required>
                                    <option value="">-- Pilih Vendor --</option>
                                    <option v-for="s in suppliers" :key="s.id" :value="s.id">
                                        {{ s.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.supplier_id" class="mt-1 text-xs text-red-600">{{ form.errors.supplier_id }}</div>
                            </div>

                            <!-- DATE -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Order</label>
                                <input type="date" v-model="form.order_date" class="w-full rounded border-gray-300 text-sm" required />
                                <div v-if="form.errors.order_date" class="mt-1 text-xs text-red-600">{{ form.errors.order_date }}</div>
                            </div>
                        </div>

                        <hr class="border-gray-200" />

                        <!-- ITEMS TABLE -->
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-3">Item Pembelian</h3>

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Produk</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qty</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Harga Satuan</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Subtotal</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(item, i) in form.items" :key="i">
                                            <td class="px-4 py-2">
                                                <select v-model="item.product_id" @change="onProductChange(item)" class="w-full rounded border-gray-300 text-sm" required>
                                                    <option value="">-- Pilih Produk --</option>
                                                    <option v-for="p in products" :key="p.id" :value="p.id">
                                                        {{ p.code }} - {{ p.name }}
                                                    </option>
                                                </select>
                                            </td>

                                            <td class="px-4 py-2">
                                                <input type="number" v-model="item.qty" class="w-full rounded border-gray-300 text-sm" min="1" required />
                                            </td>

                                            <td class="px-4 py-2">
                                                <div class="relative">
                                                    <span class="absolute left-2 top-2 text-gray-500 text-xs">Rp</span>
                                                    <input type="number" v-model="item.unit_price" class="w-full rounded border-gray-300 text-sm pl-8 text-right" min="0" required />
                                                </div>
                                            </td>

                                            <td class="px-4 py-2 text-right font-medium text-gray-900">
                                                {{ formatCurrency(subtotal(item)) }}
                                            </td>

                                            <td class="px-4 py-2 text-center">
                                                <button @click="removeItem(i)"
                                                        type="button"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Hapus Baris">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="5" class="px-4 py-2">
                                                <button type="button"
                                                        @click="addItem"
                                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                                    + Tambah Baris
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- TOTAL -->
                        <div class="flex justify-end items-center gap-4 border-t pt-4">
                            <div class="text-xl font-bold text-gray-800">
                                Total: {{ formatCurrency(total()) }}
                            </div>
                        </div>

                        <!-- SUBMIT -->
                        <div class="flex justify-end gap-3">
                            <Link :href="route('purchase.index')" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50 text-sm font-medium">
                                Batal
                            </Link>
                            <button type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-bold shadow disabled:opacity-50">
                                Simpan RFQ
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
