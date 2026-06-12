<template>
  <div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900">My Orders</h1>
        <p class="text-gray-500 mt-1">Track and manage your orders</p>
      </div>

      <!-- Status Filter Tabs -->
      <div class="flex items-center gap-1 bg-white rounded-xl border border-gray-100 shadow-sm p-1 mb-6 overflow-x-auto">
        <button
          v-for="tab in statusTabs"
          :key="tab.value"
          @click="setStatusFilter(tab.value)"
          :class="[
            'flex-shrink-0 flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors whitespace-nowrap',
            activeStatus === tab.value
              ? 'bg-indigo-600 text-white shadow-sm'
              : 'text-gray-600 hover:bg-gray-50'
          ]"
        >
          {{ tab.label }}
          <span
            v-if="tab.count !== undefined"
            :class="[
              'text-xs font-bold px-1.5 py-0.5 rounded-full',
              activeStatus === tab.value ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-500'
            ]"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>

      <!-- Empty State -->
      <div v-if="!orders?.data?.length" class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-5">
          <svg class="w-10 h-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-2">No orders yet</h3>
        <p class="text-gray-500 text-sm mb-6">
          {{ activeStatus === 'all' ? "You haven't placed any orders." : `No ${activeStatus} orders found.` }}
        </p>
        <Link href="/products" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors text-sm">
          Start Shopping
        </Link>
      </div>

      <!-- Orders Table -->
      <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Items</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Payment</th>
                <th class="px-6 py-3.5"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="order in orders.data"
                :key="order.id"
                class="hover:bg-gray-50 transition-colors group"
              >
                <td class="px-6 py-4">
                  <span class="font-bold text-indigo-600 text-sm">#{{ order.order_number }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ formatDate(order.created_at) }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1">
                    <div class="flex -space-x-1">
                      <img
                        v-for="(item, i) in order.items?.slice(0, 3)"
                        :key="i"
                        :src="item.image || '/images/placeholder.png'"
                        :alt="item.name"
                        class="w-7 h-7 rounded-full border-2 border-white object-cover"
                      />
                    </div>
                    <span class="text-xs text-gray-500 ml-1">
                      {{ order.items_count || order.items?.length || 0 }} item{{ (order.items_count || order.items?.length) !== 1 ? 's' : '' }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="font-bold text-gray-900 text-sm">{{ currencySymbol }}{{ formatPrice(order.total) }}</span>
                </td>
                <td class="px-6 py-4">
                  <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full', statusClass(order.status)]">
                    <span :class="['w-1.5 h-1.5 rounded-full', statusDotClass(order.status)]"></span>
                    {{ statusLabel(order.status) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="['inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full', paymentClass(order.payment_status)]">
                    {{ paymentLabel(order.payment_status) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <Link
                    :href="`/orders/${order.id}`"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1 opacity-0 group-hover:opacity-100"
                  >
                    View
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="sm:hidden divide-y divide-gray-100">
          <div
            v-for="order in orders.data"
            :key="order.id"
            class="p-4"
          >
            <div class="flex items-start justify-between mb-2">
              <div>
                <span class="font-bold text-indigo-600 text-sm">#{{ order.order_number }}</span>
                <p class="text-xs text-gray-500 mt-0.5">{{ formatDate(order.created_at) }}</p>
              </div>
              <span :class="['inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full', statusClass(order.status)]">
                {{ statusLabel(order.status) }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <div class="text-sm">
                <span class="text-gray-500">{{ order.items_count || 0 }} items</span>
                <span class="mx-2 text-gray-300">•</span>
                <span class="font-bold text-gray-900">{{ currencySymbol }}{{ formatPrice(order.total) }}</span>
              </div>
              <Link :href="`/orders/${order.id}`" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                View Details
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="orders?.last_page > 1" class="flex items-center justify-between mt-6">
        <p class="text-sm text-gray-500">
          Showing {{ orders.from }}–{{ orders.to }} of {{ orders.total }} orders
        </p>
        <div class="flex items-center gap-1">
          <Link
            v-if="orders.prev_page_url"
            :href="orders.prev_page_url"
            class="px-3 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1 text-gray-600"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Prev
          </Link>
          <Link
            v-if="orders.next_page_url"
            :href="orders.next_page_url"
            class="px-3 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1 text-gray-600"
          >
            Next
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../../Layouts/ShopLayout.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  orders: {
    type: Object,
    default: () => ({ data: [], total: 0, current_page: 1, last_page: 1 })
  },
  statusCounts: { type: Object, default: () => ({}) },
  currentStatus: { type: String, default: 'all' }
})

const page = usePage()
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const activeStatus = ref(props.currentStatus || 'all')

const statusTabs = computed(() => [
  { value: 'all', label: 'All Orders', count: props.statusCounts?.all },
  { value: 'pending', label: 'Pending', count: props.statusCounts?.pending },
  { value: 'processing', label: 'Processing', count: props.statusCounts?.processing },
  { value: 'shipped', label: 'Shipped', count: props.statusCounts?.shipped },
  { value: 'delivered', label: 'Delivered', count: props.statusCounts?.delivered },
  { value: 'cancelled', label: 'Cancelled', count: props.statusCounts?.cancelled }
])

function setStatusFilter(status) {
  activeStatus.value = status
  router.get('/orders', status !== 'all' ? { status } : {}, { preserveState: true, replace: true })
}

function formatPrice(price) {
  return Number(price || 0).toFixed(2)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

function statusLabel(status) {
  const labels = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled',
    refunded: 'Refunded'
  }
  return labels[status] || status
}

function statusClass(status) {
  const classes = {
    pending: 'bg-yellow-50 text-yellow-700',
    processing: 'bg-blue-50 text-blue-700',
    shipped: 'bg-indigo-50 text-indigo-700',
    delivered: 'bg-green-50 text-green-700',
    cancelled: 'bg-rose-50 text-rose-700',
    refunded: 'bg-gray-50 text-gray-700'
  }
  return classes[status] || 'bg-gray-50 text-gray-700'
}

function statusDotClass(status) {
  const classes = {
    pending: 'bg-yellow-500',
    processing: 'bg-blue-500',
    shipped: 'bg-indigo-500',
    delivered: 'bg-green-500',
    cancelled: 'bg-rose-500',
    refunded: 'bg-gray-500'
  }
  return classes[status] || 'bg-gray-400'
}

function paymentLabel(status) {
  const labels = { pending: 'Unpaid', paid: 'Paid', failed: 'Failed', refunded: 'Refunded' }
  return labels[status] || status
}

function paymentClass(status) {
  const classes = {
    pending: 'bg-yellow-50 text-yellow-700',
    paid: 'bg-green-50 text-green-700',
    failed: 'bg-rose-50 text-rose-700',
    refunded: 'bg-gray-50 text-gray-700'
  }
  return classes[status] || 'bg-gray-50 text-gray-700'
}
</script>
