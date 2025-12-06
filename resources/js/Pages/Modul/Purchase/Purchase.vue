<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  orders: Object,
  currentTab: String
});

// --- EMAIL PREVIEW LOGIC ---
const showEmailModal = ref(false);
const emailData = ref({ to: '', subject: '', html: '' });
const isPreviewLoading = ref(false);
const selectedOrderId = ref(null);

const openEmailPreview = async (orderId) => {
    selectedOrderId.value = orderId;
    isPreviewLoading.value = true;
    showEmailModal.value = true;
    emailData.value = { to: 'Loading...', subject: 'Loading...', html: '<p class="p-4 text-gray-500">Memuat pratinjau email...</p>' };

    try {
        const response = await fetch(route('purchase.preview_email', orderId));
        const data = await response.json();
        emailData.value = data;
    } catch (error) {
        emailData.value = { to: '-', subject: 'Error', html: '<p class="text-red-500">Gagal memuat pratinjau.</p>' };
        console.error(error);
    } finally {
        isPreviewLoading.value = false;
    }
};

const closeEmailModal = () => {
    showEmailModal.value = false;
    selectedOrderId.value = null;
};

const confirmSendEmail = () => {
    if (!selectedOrderId.value) return;
    
    router.post(route('purchase.send', selectedOrderId.value), {}, {
        onFinish: () => closeEmailModal()
    });
};

const cancelOrder = (id) => {
    if (confirm('Yakin ingin membatalkan pesanan ini? Status akan menjadi Cancelled.')) {
        router.post(route('purchase.cancel', id));
    }
};

// Status label styling
function statusClass(status) {
  switch (status) {
    case "RFQ":
      return "bg-gray-100 text-gray-800";
    case "Sent":
      return "bg-blue-100 text-blue-800";
    case "Purchase Order":
      return "bg-purple-100 text-purple-800";
    case "Received":
      return "bg-green-100 text-green-800";
    case "Cancelled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
}

// Helper format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};
</script>

<template>
  <Head title="Purchase" />

  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">
            {{ currentTab === 'po' ? 'Purchase Orders' : 'Requests for Quotation' }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            {{ currentTab === 'po' ? 'Daftar order pembelian yang sudah dikonfirmasi.' : 'Daftar penawaran ke vendor (Draft/Sent).' }}
          </p>
        </div>

        <div class="flex gap-2">
          <Link
            :href="route('vendors.index')"
            class="rounded bg-white border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            Kelola Vendor
          </Link>
          <Link
            :href="route('purchase.create')"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
          >
            + Buat Purchase Order
          </Link>
        </div>
      </div>
    </template>

    <!-- CONTENT -->
    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        <!-- FLASH MESSAGE -->
        <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ $page.props.flash?.success }}</span>
        </div>

        <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ $page.props.flash?.error }}</span>
        </div>
        
        <!-- TABS -->
        <div class="flex space-x-4 mb-4 border-b border-gray-200">
            <Link
                :href="route('purchase.index', { tab: 'rfq' })"
                class="pb-2 px-1 text-sm font-medium border-b-2 transition-colors duration-200"
                :class="currentTab === 'rfq' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            >
                Requests for Quotation
            </Link>
            <Link
                :href="route('purchase.index', { tab: 'po' })"
                class="pb-2 px-1 text-sm font-medium border-b-2 transition-colors duration-200"
                :class="currentTab === 'po' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            >
                Purchase Orders
            </Link>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <!-- TABEL -->
          <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
              <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                  <th class="p-3 border text-left">No. PO</th>
                  <th class="p-3 border text-left">Supplier</th>
                  <th class="p-3 border text-left">Tanggal</th>
                  <th class="p-3 border text-center">Status</th>
                  <th class="p-3 border text-right">Total</th>
                  <th class="p-3 border text-center">Aksi</th>
                </tr>
              </thead>

              <tbody class="text-sm">
                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">

                  <td class="p-3 border font-mono text-blue-600 font-semibold">
                      {{ order.po_number }}
                  </td>
                  <td class="p-3 border font-medium text-gray-900">{{ order.supplier?.name || '-' }}</td>
                  <td class="p-3 border">{{ order.order_date }}</td>

                  <!-- STATUS BADGE -->
                  <td class="p-3 border text-center">
                    <span
                      class="px-2 py-1 rounded text-xs font-bold"
                      :class="statusClass(order.status)"
                    >
                      {{ order.status }}
                    </span>
                  </td>

                  <td class="p-3 border text-right font-mono">{{ formatCurrency(order.total_amount) }}</td>

                  <!-- ACTION BUTTONS -->
                  <td class="p-3 border text-center">
                    <div class="flex justify-center items-center gap-2">
                        
                        <!-- SEND (RFQ -> Sent) -->
                        <button
                        v-if="order.status === 'RFQ'"
                        @click="openEmailPreview(order.id)"
                        class="text-purple-600 hover:underline text-xs font-semibold flex items-center gap-1"
                        title="Tandai sudah dikirim ke vendor"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 004.863 9.5H10a.75.75 0 010 1.5H4.863a1.5 1.5 0 00-1.17.336l-1.414 4.925a.75.75 0 00.826.95 28.89 28.89 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
                            </svg>
                            Send
                        </button>

                        <!-- CONFIRM -->
                        <Link
                        v-if="['RFQ', 'Sent'].includes(order.status)"
                        :href="route('purchase.confirm', order.id)"
                        method="post"
                        as="button"
                        class="text-green-600 hover:underline text-xs font-semibold"
                        >
                        Confirm
                        </Link>

                        <!-- CANCEL -->
                        <button
                        v-if="['RFQ', 'Sent', 'Purchase Order'].includes(order.status)"
                        @click="cancelOrder(order.id)"
                        class="text-red-500 hover:underline text-xs font-semibold"
                        title="Batalkan Pesanan"
                        >
                        Cancel
                        </button>

                        <!-- RECEIVE -->
                        <Link
                        v-if="order.status === 'Purchase Order'"
                        :href="route('purchase.receive', order.id)"
                        method="post"
                        as="button"
                        class="text-blue-600 hover:underline text-xs font-semibold"
                        >
                        Receive
                        </Link>

                        <!-- CREATE BILL (Received) -->
                        <Link
                        v-if="order.status === 'Received'"
                        :href="route('purchase.create_bill', order.id)"
                        method="post"
                        as="button"
                        class="text-purple-600 hover:underline text-xs font-semibold flex items-center gap-1"
                        title="Buat Tagihan Vendor"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                <path fill-rule="evenodd" d="M1 4a1 1 0 011-1h16a1 1 0 011 1v8a1 1 0 01-1 1H2a1 1 0 01-1-1V4zm12 4a3 3 0 11-6 0 3 3 0 016 0zM4 9a1 1 0 100-2 1 1 0 000 2zm13-1a1 1 0 11-2 0 1 1 0 012 0zM1.75 14.5a.75.75 0 000 1.5c4.417 0 8.693.603 12.749 1.73 1.111.309 2.251-.512 2.251-1.696v-.784a.75.75 0 00-1.5 0v.784a.272.272 0 01-.35.25A49.043 49.043 0 001.75 14.5z" clip-rule="evenodd" />
                            </svg>
                            Create Bill
                        </Link>

                        <!-- PRINT (Manual Link) -->
                        <a 
                            v-if="order.id"
                            :href="`/purchase/${order.id}/print`" 
                            target="_blank"
                            class="text-gray-500 hover:text-gray-800 text-xs font-semibold"
                        >
                            Print
                        </a>

                        <!-- EDIT -->
                        <Link
                        v-if="order.status === 'RFQ'"
                        :href="route('purchase.edit', order.id)"
                        class="text-blue-600 hover:underline text-xs font-semibold"
                        >
                        Edit
                        </Link>

                        <!-- DELETE -->
                        <Link
                        v-if="order.status === 'RFQ'"
                        :href="route('purchase.delete', order.id)"
                        method="delete"
                        as="button"
                        class="text-red-600 hover:underline text-xs font-semibold"
                        >
                        Hapus
                        </Link>

                    </div>
                  </td>

                </tr>
                <tr v-if="orders.data.length === 0">
                    <td colspan="6" class="p-6 text-center text-gray-500">
                        Belum ada data Purchase Order.
                    </td>
                </tr>
              </tbody>

            </table>
          </div>

          <!-- PAGINATION -->
            <div class="mt-4 flex justify-between items-center" v-if="orders.total > 0">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ orders.from }} sampai {{ orders.to }} dari {{ orders.total }} data.
                </div>
                <div class="flex gap-1">
                    <Link
                        v-for="(link, k) in orders.links"
                        :key="k"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 border rounded text-sm"
                        :class="{
                            'bg-blue-600 text-white border-blue-600': link.active,
                            'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                            'opacity-50 cursor-not-allowed': !link.url
                        }"
                    />
                </div>
            </div>

        </div>
      </div>
    </div>

    <!-- EMAIL PREVIEW MODAL -->
    <div v-if="showEmailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Pratinjau Email Penawaran</h3>
                <button @click="closeEmailModal" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-100">
                
                <!-- Email Meta -->
                <div class="bg-white p-4 rounded shadow-sm mb-4 space-y-2 border">
                    <div class="flex">
                        <span class="w-20 text-gray-500 font-medium text-sm">Kepada:</span>
                        <span class="text-gray-800 text-sm font-mono">{{ emailData.to }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-20 text-gray-500 font-medium text-sm">Subjek:</span>
                        <span class="text-gray-800 text-sm font-semibold">{{ emailData.subject }}</span>
                    </div>
                </div>

                <!-- Email Content Preview -->
                <div class="bg-white border rounded shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b text-xs text-gray-500 uppercase font-bold tracking-wider">
                        Isi Email
                    </div>
                    <div class="p-4 prose max-w-none text-sm" v-html="emailData.html"></div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                <button 
                    @click="closeEmailModal" 
                    class="px-4 py-2 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 text-sm font-medium"
                >
                    Batal
                </button>
                <button 
                    @click="confirmSendEmail" 
                    :disabled="isPreviewLoading"
                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-medium flex items-center gap-2 disabled:opacity-50"
                >
                    <svg v-if="!isPreviewLoading" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                    <span v-if="isPreviewLoading">Memuat...</span>
                    <span v-else>Kirim Email Sekarang</span>
                </button>
            </div>

        </div>
    </div>

  </AuthenticatedLayout>
</template>
