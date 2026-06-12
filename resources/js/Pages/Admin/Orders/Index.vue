<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  orders: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  filters: { type: Object, default: () => ({}) },
})

const filterForm = reactive({
  search: props.filters.search ?? '',
  status: props.filters.status ?? '',
  payment_status: props.filters.payment_status ?? '',
  date_from: props.filters.date_from ?? '',
  date_to: props.filters.date_to ?? '',
})

function applyFilters() {
  router.get(route('admin.orders.index'), filterForm, { preserveState: true, replace: true })
}

function clearFilters() {
  Object.assign(filterForm, { search: '', status: '', payment_status: '', date_from: '', date_to: '' })
  applyFilters()
}

function exportCsv() {
  // Export disabled — placeholder
  const params = new URLSearchParams(filterForm)
  window.location.href = route('admin.orders.export') + '?' + params.toString()
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function orderStatusClass(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700',
    processing: 'bg-blue-100 text-blue-700',
    shipped: 'bg-purple-100 text-purple-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-600',
    refunded: 'bg-gray-100 text-gray-600',
  }
  return map[status] ?? 'bg-gray-100 text-gray-600'
}

function paymentStatusClass(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700',
    paid: 'bg-green-100 text-green-700',
    failed: 'bg-red-100 text-red-600',
    refunded: 'bg-gray-100 text-gray-600',
  }
  return map[status] ?? 'bg-gray-100 text-gray-600'
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
        <p class="text-sm text-gray-400 mt-0.5">Track and manage customer orders</p>
      </div>
      <button
        disabled
        class="inline-flex items-center gap-2 border border-gray-200 text-gray-400 px-4 py-2.5 rounded-xl text-sm font-medium bg-white cursor-not-allowed"
        title="Export coming soon"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Export CSV
      </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex flex-wrap gap-3">
        <!-- Search -->
        <div class="relative flex-1 min-w-48">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="filterForm.search"
            type="text"
            placeholder="Order # or customer name..."
            class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
            @keyup.enter="applyFilters"
          />
        </div>

        <!-- Order Status -->
        <select
          v-model="filterForm.status"
          class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-white"
        >
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
          <option value="refunded">Refunded</option>
        </select>

        <!-- Payment Status -->
        <select
          v-model="filterForm.payment_status"
          class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-white"
        >
          <option value="">All Payments</option>
          <option value="pending">Pending</option>
          <option value="paid">Paid</option>
          <option value="failed">Failed</option>
          <option value="refunded">Refunded</option>
        </select>

        <!-- Date Range -->
        <input
          v-model="filterForm.date_from"
          type="date"
          class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
        />
        <input
          v-model="filterForm.date_to"
          type="date"
          class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
        />

        <button
          @click="applyFilters"
          class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700 transition"
        >
          Filter
        </button>
        <button
          @click="clearFilters"
          class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 transition"
        >
          Clear
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3.5 font-medium">Order #</th>
              <th class="px-5 py-3.5 font-medium">Customer</th>
              <th class="px-5 py-3.5 font-medium">Status</th>
              <th class="px-5 py-3.5 font-medium">Payment</th>
              <th class="px-5 py-3.5 font-medium">Items</th>
              <th class="px-5 py-3.5 font-medium">Total</th>
              <th class="px-5 py-3.5 font-medium">Date</th>
              <th class="px-5 py-3.5 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="order in orders.data"
              :key="order.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3.5">
                <span class="font-semibold text-indigo-600">#{{ order.order_number }}</span>
              </td>
              <td class="px-5 py-3.5">
                <p class="font-medium text-gray-800">{{ order.user?.name ?? order.guest_name ?? '—' }}</p>
                <p class="text-xs text-gray-400">{{ order.user?.email ?? order.guest_email ?? '' }}</p>
              </td>
              <td class="px-5 py-3.5">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="orderStatusClass(order.status)"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="paymentStatusClass(order.payment_status)"
                >
                  {{ order.payment_status }}
                </span>
              </td>
              <td class="px-5 py-3.5 text-gray-600">
                {{ order.items_count ?? order.items?.length ?? 0 }}
              </td>
              <td class="px-5 py-3.5">
                <span class="font-semibold text-gray-800">${{ Number(order.total).toFixed(2) }}</span>
              </td>
              <td class="px-5 py-3.5 text-gray-400 text-xs whitespace-nowrap">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-5 py-3.5">
                <div class="flex justify-end">
                  <Link
                    :href="route('admin.orders.show', order.id)"
                    class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-lg hover:bg-indigo-50 transition"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View
                  </Link>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="!orders.data?.length">
              <td colspan="8" class="px-5 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-400 font-medium">No orders found</p>
                <p class="text-gray-300 text-sm mt-1">Try adjusting your filters.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="orders.last_page > 1"
        class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm"
      >
        <p class="text-gray-400 text-xs">
          Showing {{ orders.from }}–{{ orders.to }} of {{ orders.total }} orders
        </p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in orders.links"
            :key="link.label"
            :href="link.url ?? '#'"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-medium transition',
              link.active
                ? 'bg-indigo-600 text-white'
                : link.url
                  ? 'text-gray-600 hover:bg-gray-100'
                  : 'text-gray-300 cursor-not-allowed pointer-events-none',
            ]"
            v-html="link.label"
          />
        </div>
      </div>
    </div>
  </div>
</template>
