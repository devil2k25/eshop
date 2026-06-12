<template>
  <AdminLayout title="Orders">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Orders</h2>
        <p class="text-sm text-gray-400">Track and manage customer orders</p>
      </div>
      <button
        @click="exportCsv"
        class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition"
      >
        📤 Export CSV
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
        <input
          v-model="filterForm.search"
          type="text"
          placeholder="Order # or customer name..."
          class="flex-1 min-w-0 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
          @keyup.enter="applyFilters"
        />
        <select
          v-model="filterForm.status"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
          <option value="refunded">Refunded</option>
        </select>
        <select
          v-model="filterForm.payment_status"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Payment</option>
          <option value="pending">Pending</option>
          <option value="paid">Paid</option>
          <option value="failed">Failed</option>
          <option value="refunded">Refunded</option>
        </select>
        <input
          v-model="filterForm.date_from"
          type="date"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        />
        <input
          v-model="filterForm.date_to"
          type="date"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        />
        <button @click="applyFilters" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700">Filter</button>
        <button @click="clearFilters" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200">Clear</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3 font-medium">Order #</th>
              <th class="px-5 py-3 font-medium">Customer</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium">Payment</th>
              <th class="px-5 py-3 font-medium">Items</th>
              <th class="px-5 py-3 font-medium">Total</th>
              <th class="px-5 py-3 font-medium">Date</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="order in orders.data"
              :key="order.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <span class="font-semibold text-indigo-600">#{{ order.order_number }}</span>
              </td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ order.user?.name ?? order.guest_name }}</p>
                <p class="text-xs text-gray-400">{{ order.user?.email ?? order.guest_email }}</p>
              </td>
              <td class="px-5 py-3">
                <span :class="orderStatusBadge(order.status)">{{ order.status }}</span>
              </td>
              <td class="px-5 py-3">
                <span :class="paymentStatusBadge(order.payment_status)">{{ order.payment_status }}</span>
              </td>
              <td class="px-5 py-3 text-gray-600">{{ order.items_count ?? 0 }}</td>
              <td class="px-5 py-3 font-semibold text-gray-800">${{ order.total }}</td>
              <td class="px-5 py-3 text-gray-400 text-xs">{{ formatDate(order.created_at) }}</td>
              <td class="px-5 py-3">
                <div class="flex justify-end">
                  <Link
                    :href="`/admin/orders/${order.id}`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >View</Link>
                </div>
              </td>
            </tr>
            <tr v-if="!orders.data?.length">
              <td colspan="8" class="px-5 py-12 text-center text-gray-400">No orders found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="orders.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ orders.from }}–{{ orders.to }} of {{ orders.total }} orders</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in orders.links"
            :key="link.label"
            :href="link.url ?? '#'"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-medium transition',
              link.active ? 'bg-indigo-600 text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed pointer-events-none'
            ]"
            v-html="link.label"
          />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: undefined })

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
  router.get('/admin/orders', filterForm, { preserveState: true, replace: true })
}

function clearFilters() {
  Object.assign(filterForm, { search: '', status: '', payment_status: '', date_from: '', date_to: '' })
  applyFilters()
}

function exportCsv() {
  const params = new URLSearchParams(filterForm)
  window.location.href = `/admin/orders/export?${params}`
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function orderStatusBadge(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    processing: 'bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    shipped: 'bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    delivered: 'bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    cancelled: 'bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    refunded: 'bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
  }
  return map[status] ?? 'bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize'
}

function paymentStatusBadge(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    paid: 'bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    failed: 'bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    refunded: 'bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
  }
  return map[status] ?? 'bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize'
}
</script>
