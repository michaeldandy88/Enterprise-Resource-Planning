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
    order_date: "",
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

// Hitung subtotal
function subtotal(item) {
    return item.qty * item.unit_price;
}

// Hitung total
function total() {
    return form.items.reduce((sum, i) => sum + subtotal(i), 0);
}
</script>


<template>
    <Head title="Create Purchase Order" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Create Purchase Order
            </h2>
        </template>

        <div class="max-w-5xl mx-auto bg-white p-6 shadow rounded">

            <!-- FORM -->
            <form @submit.prevent="form.post('/purchase/store')">

                <!-- PO NUMBER -->
                <div class="mb-4">
                    <label class="text-sm text-gray-700">PO Number</label>
                    <input v-model="form.po_number" class="input" required />
                </div>

                <!-- SUPPLIER -->
                <div class="mb-4">
                    <label class="text-sm text-gray-700">Supplier</label>
                    <select v-model="form.supplier_id" class="input" required>
                        <option value="">-- pilih supplier --</option>
                        <option v-for="s in suppliers" :key="s.id" :value="s.id">
                            {{ s.name }}
                        </option>
                    </select>
                </div>

                <!-- DATE -->
                <div class="mb-4">
                    <label class="text-sm text-gray-700">Order Date</label>
                    <input type="date" v-model="form.order_date" class="input" required />
                </div>

                <!-- ITEMS TABLE -->
                <h3 class="font-semibold text-gray-700 mb-2">Items</h3>

                <table class="w-full border mb-4">
                    <thead>
                        <tr class="bg-gray-100 text-sm">
                            <th class="p-2 border">Product</th>
                            <th class="p-2 border">Qty</th>
                            <th class="p-2 border">Unit Price</th>
                            <th class="p-2 border">Subtotal</th>
                            <th class="p-2 border">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(item, i) in form.items" :key="i">
                            <td class="p-2 border">
                                <select v-model="item.product_id" class="input">
                                    <option value="">-- pilih --</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">
                                        {{ p.name }}
                                    </option>
                                </select>
                            </td>

                            <td class="p-2 border">
                                <input type="number" v-model="item.qty" class="input" min="1" />
                            </td>

                            <td class="p-2 border">
                                <input type="number" v-model="item.unit_price" class="input" min="0" />
                            </td>

                            <td class="p-2 border">
                                Rp {{ subtotal(item) }}
                            </td>

                            <td class="p-2 border text-center">
                                <button @click="removeItem(i)"
                                        type="button"
                                        class="px-2 py-1 bg-red-500 text-white rounded text-xs">
                                    X
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button"
                        @click="addItem"
                        class="px-3 py-1 bg-gray-500 text-white rounded mb-4">
                    + Add Item
                </button>

                <!-- TOTAL -->
                <div class="text-right text-lg font-semibold mb-4">
                    Total: Rp {{ total() }}
                </div>

                <!-- SUBMIT -->
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save RFQ
                </button>
            </form>

        </div>
    </AuthenticatedLayout>
</template>

<style>
.input {
    @apply w-full border rounded px-2 py-1;
}
</style>
