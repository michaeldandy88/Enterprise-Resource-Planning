<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from 'vue';

const props = defineProps({
  invoice: Object
});

const showPaymentModal = ref(false);
const paymentForm = useForm({
    status: 'paid',
    payment_date: new Date().toISOString().substr(0, 10),
    payment_method: 'Bank Transfer'
});

const submitPayment = () => {
    paymentForm.post(route('invoices.update_status', props.invoice.id), {
        onSuccess: () => showPaymentModal.value = false
    });
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Invoice: {{ invoice.invoice_number }}
        </h2>
        <Link :href="route('invoices.index')" class="text-sm text-gray-600 hover:underline">
          ← Kembali ke List
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        
        <!-- Status Bar -->
        <div class="mb-4 flex items-center justify-between bg-white p-4 shadow sm:rounded-lg">
            <div class="flex items-center gap-2">
                <span class="text-gray-600 font-medium">Status:</span>
                <span :class="{
                    'bg-gray-100 text-gray-800': invoice.status === 'draft',
                    'bg-blue-100 text-blue-800': invoice.status === 'posted',
                    'bg-green-100 text-green-800': invoice.status === 'paid',
                }" class="px-3 py-1 rounded text-sm font-bold uppercase">
                    {{ invoice.status }}
                </span>
            </div>
            <div class="space-x-2">
                <Link
                    v-if="invoice.status === 'draft'"
                    :href="route('invoices.update_status', invoice.id)"
                    method="post"
                    :data="{ status: 'posted' }"
                    as="button"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-semibold"
                >
                    Post / Confirm
                </Link>
                <button
                    v-if="invoice.status === 'posted'"
                    @click="showPaymentModal = true"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-semibold"
                >
                    Register Payment
                </button>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-8">
            
            <!-- Header Info -->
            <div class="flex justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">INVOICE</h1>
                    <p class="text-gray-500">{{ invoice.invoice_number }}</p>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-gray-700">{{ invoice.customer?.name }}</h3>
                    <p class="text-sm text-gray-500">{{ invoice.customer?.address }}</p>
                    <p class="text-sm text-gray-500">{{ invoice.customer?.phone }}</p>
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <p class="text-sm text-gray-500">Invoice Date</p>
                    <p class="font-semibold">{{ formatDate(invoice.invoice_date) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Due Date</p>
                    <p class="font-semibold">{{ formatDate(invoice.due_date) }}</p>
                </div>
                <div v-if="invoice.sales_order">
                    <p class="text-sm text-gray-500">Source Document</p>
                    <Link :href="route('sales.edit', invoice.sales_order.id)" class="text-blue-600 hover:underline">
                        {{ invoice.sales_order.so_number }}
                    </Link>
                </div>
                <div v-if="invoice.status === 'paid'" class="col-span-2 bg-green-50 p-4 rounded border border-green-200">
                    <p class="text-green-800 font-semibold">
                        Lunas pada {{ formatDate(invoice.payment_date) }} via {{ invoice.payment_method }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full mb-8">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="text-left py-2 text-gray-600 font-bold">Description</th>
                        <th class="text-right py-2 text-gray-600 font-bold">Qty</th>
                        <th class="text-right py-2 text-gray-600 font-bold">Unit Price</th>
                        <th class="text-right py-2 text-gray-600 font-bold">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in invoice.items" :key="item.id" class="border-b border-gray-100">
                        <td class="py-3">{{ item.product?.name }}</td>
                        <td class="py-3 text-right">{{ item.qty }}</td>
                        <td class="py-3 text-right">{{ formatCurrency(item.price) }}</td>
                        <td class="py-3 text-right font-medium">{{ formatCurrency(item.subtotal) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Total -->
            <div class="flex justify-end">
                <div class="w-1/2">
                    <div class="flex justify-between py-2 border-t border-gray-200">
                        <span class="font-bold text-lg">Total</span>
                        <span class="font-bold text-lg text-blue-600">{{ formatCurrency(invoice.total_amount) }}</span>
                    </div>
                </div>
            </div>

        </div>


      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Register Payment</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                <input v-model="paymentForm.payment_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select v-model="paymentForm.payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="Cash">Cash</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Credit Card">Credit Card</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button @click="showPaymentModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                <button @click="submitPayment" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Validate</button>
            </div>
        </div>
    </div>

  </AuthenticatedLayout>
</template>
