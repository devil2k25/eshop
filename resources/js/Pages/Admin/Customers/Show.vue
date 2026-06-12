<template>
  <AdminLayout :title="`Customer: ${customer.name}`">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
      <Link href="/admin/customers" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <h2 class="text-xl font-bold text-gray-800">Customer Profile</h2>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      <!-- Customer Info -->
      <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
          <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
            {{ getInitials(customer.name) }}
          </div>
          <h3 class="text-lg font-bold text-gray-800">{{ customer.name }}</h3>
          <p class="text-sm text-gray-500 mt-1">{{ customer.email }}</p>
          <p v-if="customer.phone" class="text-sm text-gray-400 mt-0.5">{{ customer.phone }}</p>
          <span
            :class="customer.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
            class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-medium"
          >{{ customer.is_active ? 'Active' : 'Inactive' }}</span>
          <p class="text-xs text-gray-400 mt-3">Member since {{ formatDate(customer.created_at) }}</p>
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 grid grid-cols-2 gap-4">
          <div class="text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ customer.orders_count ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Orders</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold text-emerald-600">${{ customer.total_spent ?? '0.00' }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Spent</p>
          </div>
        </div>

        <!-- Addresses -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-800 mb-3">Saved Addresses</h3>
          <div v-if="customer.addresses?.length" class="space-y-3">
            <div
              v-for="addr in customer.addresses"
              :key="addr.id"
              class="p-3 bg-gray-50 rounded-xl text-sm text-gray-600 space-y-0.5"
            >
              <p class="font-medium text-gray-800">{{ addr.name }}</p>
              <p>{{ addr.line1 }}</p>
              <p v-if="addr.line2">{{ addr.line2 }}</p>
              <p>{{ addr.city }}, {{ addr.state }} {{ addr.postal_code }}</p>
              <p>{{ addr.country }}</p>
              <span v-if="addr.is_default" class="inline-block text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded mt-1">Default</span>
            </div>
          </div>
          <p v-else class="text-sm text-gray-400">No saved addresses</p>
        </div>
      </div>

      <!-- Order History -->
      <div class="xl:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Order History</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
                  <th class="px-5 py-3 font-medium">Order #</th>
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
                  <td class="px-5 py-3 font-semibold text-indigo-600">#{{ order.order_number }}</td>
                  <td class="px-5 py-3">
                    <span :class="orderBadge(order.status)">{{ order.status }}</span>
                  </td>
                  <td class="px-5 py-3">
                    <span :class="payBadge(order.payment_status)">{{ order.payment_status }}</span>
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
                  <td colspan="7" class="px-5 py-12 text-center text-gray-400">No orders yet.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="orders.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <p class="text-gray-400">Showing {{ orders.from }}–{{ orders.to }} of {{ orders.total }}</p>
            <div class="flex gap-1">
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
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: undefined })

const props = defineProps({
  customer: { type: Object, required: true },
  orders: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
})

function getInitials(name) {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function orderBadge(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    processing: 'bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    shipped: 'bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    delivered: 'bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    cancelled: 'bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
  }
  return map[status] ?? 'bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize'
}

function payBadge(status) {
  const map = {
    paid: 'bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    pending: 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    failed: 'bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    refunded: 'bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
  }
  return map[status] ?? 'bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium capitalize'
}
</script>
