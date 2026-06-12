<template>
  <AdminLayout :title="`Order #${order.order_number}`">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div class="flex items-center gap-3">
        <Link href="/admin/orders" class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </Link>
        <div>
          <h2 class="text-xl font-bold text-gray-800">Order #{{ order.order_number }}</h2>
          <p class="text-sm text-gray-400">{{ formatDate(order.created_at) }}</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <select
          v-model="statusForm.status"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
          <option value="refunded">Refunded</option>
        </select>
        <button
          @click="updateStatus"
          :disabled="statusForm.processing"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:opacity-90 disabled:opacity-60"
        >Save Status</button>
      </div>
    </div>

    <!-- Status Timeline -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
      <h3 class="text-sm font-semibold text-gray-600 mb-4">Order Progress</h3>
      <div class="flex items-center">
        <template v-for="(step, idx) in timeline" :key="step.key">
          <div class="flex flex-col items-center flex-1">
            <div
              :class="[
                'w-9 h-9 rounded-full flex items-center justify-center text-sm border-2 transition-colors',
                isStepComplete(step.key)
                  ? 'bg-indigo-600 border-indigo-600 text-white'
                  : isCurrentStep(step.key)
                    ? 'bg-white border-indigo-600 text-indigo-600'
                    : 'bg-white border-gray-200 text-gray-300'
              ]"
            >
              {{ step.icon }}
            </div>
            <span
              :class="[
                'text-xs mt-1.5 font-medium',
                isStepComplete(step.key) || isCurrentStep(step.key) ? 'text-indigo-600' : 'text-gray-400'
              ]"
            >{{ step.label }}</span>
          </div>
          <div
            v-if="idx < timeline.length - 1"
            :class="['flex-1 h-0.5 mb-5', isStepComplete(timeline[idx + 1].key) || isStepComplete(step.key) ? 'bg-indigo-600' : 'bg-gray-200']"
          />
        </template>
      </div>
    </div>

    <!-- Tracking -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
      <h3 class="text-sm font-semibold text-gray-600 mb-3">Tracking Number</h3>
      <div class="flex gap-3">
        <input
          v-model="trackingForm.tracking_number"
          type="text"
          placeholder="Enter tracking number"
          class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-mono"
        />
        <button
          @click="saveTracking"
          :disabled="trackingForm.processing"
          class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700 disabled:opacity-60"
        >Save</button>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
      <!-- Order Items -->
      <div class="xl:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Order Items</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr class="text-left text-xs text-gray-400 uppercase">
                  <th class="px-5 py-3 font-medium">Product</th>
                  <th class="px-5 py-3 font-medium">Variant</th>
                  <th class="px-5 py-3 font-medium">Qty</th>
                  <th class="px-5 py-3 font-medium">Price</th>
                  <th class="px-5 py-3 font-medium">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                      <div class="w-11 h-11 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                        <img v-if="item.image" :src="item.image" :alt="item.product_name" class="w-full h-full object-cover" />
                        <span v-else class="w-full h-full flex items-center justify-center text-gray-300 text-lg">📦</span>
                      </div>
                      <span class="font-medium text-gray-800">{{ item.product_name }}</span>
                    </div>
                  </td>
                  <td class="px-5 py-3 text-gray-500 text-xs">{{ item.variant_name ?? '—' }}</td>
                  <td class="px-5 py-3 text-gray-700">{{ item.quantity }}</td>
                  <td class="px-5 py-3 text-gray-700">${{ item.price }}</td>
                  <td class="px-5 py-3 font-semibold text-gray-800">${{ (Number(item.price) * item.quantity).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Sidebar Cards -->
      <div class="space-y-4">
        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-800 mb-4">Order Summary</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span>${{ order.subtotal }}</span>
            </div>
            <div v-if="order.discount_amount" class="flex justify-between text-emerald-600">
              <span>Discount</span>
              <span>-${{ order.discount_amount }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Shipping</span>
              <span>{{ order.shipping_amount ? '$' + order.shipping_amount : 'Free' }}</span>
            </div>
            <div v-if="order.tax_amount" class="flex justify-between text-gray-600">
              <span>Tax</span>
              <span>${{ order.tax_amount }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-800 border-t border-gray-100 pt-2 mt-2 text-base">
              <span>Total</span>
              <span>${{ order.total }}</span>
            </div>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-800 mb-3">Customer</h3>
          <div class="space-y-1 text-sm">
            <p class="font-medium text-gray-800">{{ order.user?.name ?? order.guest_name }}</p>
            <p class="text-gray-500">{{ order.user?.email ?? order.guest_email }}</p>
            <p v-if="order.user" class="text-gray-400 text-xs">{{ order.user.orders_count ?? 0 }} orders total</p>
            <Link
              v-if="order.user_id"
              :href="`/admin/customers/${order.user_id}`"
              class="text-indigo-600 text-xs hover:underline"
            >View Customer →</Link>
          </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-800 mb-3">Shipping Address</h3>
          <div class="text-sm text-gray-600 space-y-0.5" v-if="order.shipping_address">
            <p class="font-medium text-gray-800">{{ order.shipping_address.name }}</p>
            <p>{{ order.shipping_address.line1 }}</p>
            <p v-if="order.shipping_address.line2">{{ order.shipping_address.line2 }}</p>
            <p>{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.postal_code }}</p>
            <p>{{ order.shipping_address.country }}</p>
            <p v-if="order.shipping_address.phone" class="text-gray-400">{{ order.shipping_address.phone }}</p>
          </div>
          <p v-else class="text-sm text-gray-400">No address provided</p>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
          <h3 class="font-semibold text-gray-800 mb-3">Payment</h3>
          <div class="text-sm space-y-1.5">
            <div class="flex justify-between">
              <span class="text-gray-500">Method</span>
              <span class="font-medium text-gray-700 capitalize">{{ order.payment_method ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Status</span>
              <span :class="payBadge(order.payment_status)">{{ order.payment_status }}</span>
            </div>
            <div v-if="order.transaction_id" class="flex justify-between">
              <span class="text-gray-500">Transaction</span>
              <span class="font-mono text-xs text-gray-700">{{ order.transaction_id }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: undefined })

const props = defineProps({
  order: { type: Object, required: true },
})

const statusForm = useForm({ status: props.order.status })
const trackingForm = useForm({ tracking_number: props.order.tracking_number ?? '' })

const timeline = [
  { key: 'pending', label: 'Pending', icon: '🕐' },
  { key: 'processing', label: 'Processing', icon: '⚙️' },
  { key: 'shipped', label: 'Shipped', icon: '🚚' },
  { key: 'delivered', label: 'Delivered', icon: '✅' },
]

const statusOrder = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded']

function isStepComplete(key) {
  const current = statusOrder.indexOf(props.order.status)
  const step = statusOrder.indexOf(key)
  return step < current
}

function isCurrentStep(key) {
  return props.order.status === key
}

function updateStatus() {
  statusForm.patch(`/admin/orders/${props.order.id}/status`)
}

function saveTracking() {
  trackingForm.patch(`/admin/orders/${props.order.id}/tracking`)
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
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
