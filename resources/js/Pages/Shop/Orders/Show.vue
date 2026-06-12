<template>
  <div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Back Link -->
      <div class="mb-6">
        <Link href="/orders" class="flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Back to Orders
        </Link>
      </div>

      <!-- Order Header -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h1 class="text-xl font-extrabold text-gray-900">Order #{{ order.order_number }}</h1>
            <p class="text-sm text-gray-500 mt-1">Placed on {{ formatDate(order.created_at) }}</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full', statusClass(order.status)]">
              <span :class="['w-2 h-2 rounded-full', statusDotClass(order.status)]"></span>
              {{ statusLabel(order.status) }}
            </span>
            <span :class="['inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-full', paymentClass(order.payment_status)]">
              {{ paymentLabel(order.payment_status) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Order Timeline -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-bold text-gray-900 mb-6">Order Status</h2>
        <div class="flex items-center justify-between relative">
          <!-- Progress Line -->
          <div class="absolute left-0 right-0 top-4 h-0.5 bg-gray-100 z-0">
            <div
              class="h-full bg-indigo-600 transition-all duration-500"
              :style="{ width: `${timelineProgress}%` }"
            ></div>
          </div>

          <div
            v-for="(step, index) in timelineSteps"
            :key="step.key"
            class="relative z-10 flex flex-col items-center gap-2 flex-1"
          >
            <div :class="[
              'w-8 h-8 rounded-full border-2 flex items-center justify-center transition-all duration-300',
              isStepCompleted(step.key)
                ? 'bg-indigo-600 border-indigo-600 text-white'
                : isStepActive(step.key)
                  ? 'bg-white border-indigo-500 text-indigo-500'
                  : 'bg-white border-gray-200 text-gray-300'
            ]">
              <svg v-if="isStepCompleted(step.key)" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              <span v-else class="text-xs font-bold">{{ index + 1 }}</span>
            </div>
            <div class="text-center">
              <p :class="['text-xs font-semibold', isStepCompleted(step.key) || isStepActive(step.key) ? 'text-indigo-700' : 'text-gray-400']">
                {{ step.label }}
              </p>
              <p v-if="isStepActive(step.key)" class="text-xs text-indigo-500 font-medium">Current</p>
            </div>
          </div>
        </div>

        <!-- Cancelled state -->
        <div v-if="order.status === 'cancelled'" class="mt-4 p-3 bg-rose-50 rounded-xl text-sm text-rose-700 text-center">
          This order has been cancelled.
          <span v-if="order.cancelled_reason"> Reason: {{ order.cancelled_reason }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Shipping Address -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Shipping To
          </h2>
          <div class="text-sm text-gray-600 space-y-0.5">
            <p class="font-semibold text-gray-800">{{ order.shipping_address?.name }}</p>
            <p>{{ order.shipping_address?.address_line1 }}</p>
            <p v-if="order.shipping_address?.address_line2">{{ order.shipping_address.address_line2 }}</p>
            <p>{{ order.shipping_address?.city }}, {{ order.shipping_address?.state }} {{ order.shipping_address?.zip_code }}</p>
            <p>{{ order.shipping_address?.country }}</p>
            <p class="mt-1 text-gray-500">{{ order.shipping_address?.phone }}</p>
          </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Payment
          </h2>
          <div class="text-sm text-gray-600 space-y-1">
            <div class="flex justify-between">
              <span>Method</span>
              <span class="font-semibold text-gray-800 capitalize">{{ order.payment_method?.replace('_', ' ') || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span>Status</span>
              <span :class="['font-semibold', order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600']">
                {{ paymentLabel(order.payment_status) }}
              </span>
            </div>
            <div v-if="order.transaction_id" class="flex justify-between">
              <span>Txn ID</span>
              <span class="font-mono text-xs text-gray-700 truncate max-w-24">{{ order.transaction_id }}</span>
            </div>
          </div>
        </div>

        <!-- Order Totals -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Order Total
          </h2>
          <div class="text-sm space-y-1.5">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span>{{ currencySymbol }}{{ formatPrice(order.subtotal) }}</span>
            </div>
            <div v-if="order.discount > 0" class="flex justify-between text-green-600">
              <span>Discount</span>
              <span>−{{ currencySymbol }}{{ formatPrice(order.discount) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Shipping</span>
              <span :class="order.shipping_cost === 0 ? 'text-green-600 font-semibold' : ''">
                {{ order.shipping_cost === 0 ? 'FREE' : `${currencySymbol}${formatPrice(order.shipping_cost)}` }}
              </span>
            </div>
            <div v-if="order.tax > 0" class="flex justify-between text-gray-600">
              <span>Tax</span>
              <span>{{ currencySymbol }}{{ formatPrice(order.tax) }}</span>
            </div>
            <div class="border-t border-gray-100 pt-1.5 flex justify-between font-bold text-gray-900">
              <span>Total</span>
              <span>{{ currencySymbol }}{{ formatPrice(order.total) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-base font-bold text-gray-900">
            Order Items <span class="text-sm text-gray-500 font-normal">({{ order.items?.length || 0 }})</span>
          </h2>
        </div>
        <div class="divide-y divide-gray-50">
          <div
            v-for="item in order.items"
            :key="item.id"
            class="px-6 py-4 flex gap-4 items-start"
          >
            <!-- Image -->
            <Link :href="`/products/${item.product_slug}`" class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
              <img :src="item.image || '/images/placeholder.png'" :alt="item.name" class="w-full h-full object-cover" />
            </Link>
            <!-- Details -->
            <div class="flex-1 min-w-0">
              <Link :href="`/products/${item.product_slug}`" class="font-semibold text-gray-800 hover:text-indigo-600 transition-colors text-sm">
                {{ item.name }}
              </Link>
              <p v-if="item.variant_label" class="text-xs text-gray-500 mt-0.5">{{ item.variant_label }}</p>
              <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                <span>Qty: {{ item.quantity }}</span>
                <span class="text-gray-300">•</span>
                <span>{{ currencySymbol }}{{ formatPrice(item.price) }} each</span>
              </div>
            </div>
            <!-- Subtotal -->
            <div class="flex flex-col items-end gap-2">
              <span class="font-bold text-gray-900 text-sm">{{ currencySymbol }}{{ formatPrice(item.price * item.quantity) }}</span>
              <!-- Review link for delivered items -->
              <Link
                v-if="order.status === 'delivered' && !item.reviewed"
                :href="`/products/${item.product_slug}#reviews`"
                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1"
              >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Write Review
              </Link>
              <span v-else-if="item.reviewed" class="text-xs text-green-600 font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Reviewed
              </span>
            </div>
          </div>
        </div>

        <!-- Order Notes -->
        <div v-if="order.notes" class="px-6 py-4 bg-gray-50 border-t border-gray-100">
          <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Order Notes</p>
          <p class="text-sm text-gray-600">{{ order.notes }}</p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex flex-wrap gap-3 mt-6">
        <Link href="/products" class="px-5 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          Continue Shopping
        </Link>
        <button
          v-if="order.status === 'pending'"
          @click="cancelOrder"
          class="px-5 py-2.5 border border-rose-200 text-rose-600 font-semibold rounded-xl hover:bg-rose-50 transition-colors text-sm flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          Cancel Order
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../../Layouts/ShopLayout.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  order: { type: Object, required: true }
})

const page = usePage()
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const timelineSteps = [
  { key: 'pending', label: 'Pending' },
  { key: 'processing', label: 'Processing' },
  { key: 'shipped', label: 'Shipped' },
  { key: 'delivered', label: 'Delivered' }
]

const statusOrder = ['pending', 'processing', 'shipped', 'delivered']

const currentStatusIndex = computed(() => {
  if (props.order.status === 'cancelled') return -1
  return statusOrder.indexOf(props.order.status)
})

const timelineProgress = computed(() => {
  if (currentStatusIndex.value < 0) return 0
  return (currentStatusIndex.value / (timelineSteps.length - 1)) * 100
})

function isStepCompleted(key) {
  const stepIdx = statusOrder.indexOf(key)
  return stepIdx < currentStatusIndex.value
}

function isStepActive(key) {
  return key === props.order.status
}

function formatPrice(price) {
  return Number(price || 0).toFixed(2)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function statusLabel(status) {
  const labels = { pending: 'Pending', processing: 'Processing', shipped: 'Shipped', delivered: 'Delivered', cancelled: 'Cancelled', refunded: 'Refunded' }
  return labels[status] || status
}

function statusClass(status) {
  const classes = { pending: 'bg-yellow-50 text-yellow-700', processing: 'bg-blue-50 text-blue-700', shipped: 'bg-indigo-50 text-indigo-700', delivered: 'bg-green-50 text-green-700', cancelled: 'bg-rose-50 text-rose-700', refunded: 'bg-gray-50 text-gray-700' }
  return classes[status] || 'bg-gray-50 text-gray-700'
}

function statusDotClass(status) {
  const classes = { pending: 'bg-yellow-500', processing: 'bg-blue-500', shipped: 'bg-indigo-500', delivered: 'bg-green-500', cancelled: 'bg-rose-500', refunded: 'bg-gray-500' }
  return classes[status] || 'bg-gray-400'
}

function paymentLabel(status) {
  const labels = { pending: 'Payment Pending', paid: 'Paid', failed: 'Payment Failed', refunded: 'Refunded' }
  return labels[status] || status
}

function paymentClass(status) {
  const classes = { pending: 'bg-yellow-50 text-yellow-700', paid: 'bg-green-50 text-green-700', failed: 'bg-rose-50 text-rose-700', refunded: 'bg-gray-50 text-gray-700' }
  return classes[status] || 'bg-gray-50 text-gray-700'
}

function cancelOrder() {
  if (confirm('Are you sure you want to cancel this order?')) {
    router.patch(`/orders/${props.order.id}/cancel`, {}, { preserveScroll: true })
  }
}
</script>
