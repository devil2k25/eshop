<template>
  <div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900">Shopping Cart</h1>
        <p class="text-gray-500 mt-1">{{ items?.length || 0 }} item{{ items?.length !== 1 ? 's' : '' }} in your cart</p>
      </div>

      <!-- Empty State -->
      <div v-if="!items || !items.length" class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Your cart is empty</h2>
        <p class="text-gray-500 mb-6 max-w-sm">Looks like you haven't added anything yet. Discover amazing products and fill it up!</p>
        <Link href="/products" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors">
          Continue Shopping
        </Link>
      </div>

      <!-- Cart Content -->
      <div v-else class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="xl:col-span-2 space-y-4">
          <!-- Item Cards -->
          <div
            v-for="item in items"
            :key="item.id"
            class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 flex gap-4 shadow-sm hover:shadow-md transition-shadow"
          >
            <!-- Product Image -->
            <Link :href="`/products/${item.product_slug}`" class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
              <img
                :src="item.image || '/images/placeholder.png'"
                :alt="item.name"
                class="w-full h-full object-cover"
                @error="$event.target.src = '/images/placeholder.png'"
              />
            </Link>

            <!-- Item Details -->
            <div class="flex-1 min-w-0 flex flex-col sm:flex-row gap-3">
              <div class="flex-1 min-w-0">
                <Link :href="`/products/${item.product_slug}`" class="font-semibold text-gray-800 hover:text-indigo-600 transition-colors text-sm line-clamp-2">
                  {{ item.name }}
                </Link>
                <p v-if="item.variant_label" class="text-xs text-gray-500 mt-0.5">{{ item.variant_label }}</p>
                <p class="text-indigo-600 font-bold mt-1">{{ currencySymbol }}{{ formatPrice(item.price) }}</p>
              </div>

              <!-- Quantity + Remove -->
              <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                <!-- Quantity Selector -->
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                  <button
                    @click="updateQuantity(item, item.quantity - 1)"
                    :disabled="item.quantity <= 1 || updatingId === item.id"
                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 hover:text-indigo-600 font-bold text-sm transition-colors disabled:opacity-40"
                  >−</button>
                  <span class="px-3 py-1.5 text-sm font-semibold min-w-[2.5rem] text-center border-x border-gray-200">
                    <span v-if="updatingId === item.id">
                      <svg class="w-4 h-4 animate-spin mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                    <span v-else>{{ item.quantity }}</span>
                  </span>
                  <button
                    @click="updateQuantity(item, item.quantity + 1)"
                    :disabled="updatingId === item.id"
                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 hover:text-indigo-600 font-bold text-sm transition-colors disabled:opacity-40"
                  >+</button>
                </div>

                <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                  <!-- Subtotal -->
                  <span class="font-bold text-gray-900 text-sm sm:text-base whitespace-nowrap">
                    {{ currencySymbol }}{{ formatPrice(item.price * item.quantity) }}
                  </span>
                  <!-- Remove -->
                  <button
                    @click="removeItem(item)"
                    class="text-gray-300 hover:text-rose-500 transition-colors"
                    title="Remove item"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Continue Shopping -->
          <div class="flex items-center justify-between pt-2">
            <Link href="/products" class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              Continue Shopping
            </Link>
            <button @click="clearCart" class="text-sm text-gray-400 hover:text-rose-500 transition-colors">Clear Cart</button>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="xl:col-span-1">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>

            <!-- Coupon -->
            <div class="mb-5">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Promo Code</label>
              <div v-if="!couponCode">
                <div class="flex gap-2">
                  <input
                    v-model="couponInput"
                    type="text"
                    placeholder="Enter coupon code"
                    class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300"
                    @keyup.enter="applyCoupon"
                  />
                  <button
                    @click="applyCoupon"
                    :disabled="applyingCoupon || !couponInput"
                    class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                  >
                    Apply
                  </button>
                </div>
                <p v-if="couponError" class="text-xs text-rose-600 mt-1">{{ couponError }}</p>
              </div>
              <div v-else class="flex items-center justify-between p-3 bg-green-50 rounded-xl border border-green-200">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                  <span class="text-sm font-bold text-green-700">{{ couponCode }}</span>
                </div>
                <button @click="removeCoupon" class="text-xs text-rose-500 hover:text-rose-700 font-medium">Remove</button>
              </div>
            </div>

            <!-- Price Breakdown -->
            <div class="space-y-3 mb-5 text-sm">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span class="font-semibold text-gray-900">{{ currencySymbol }}{{ formatPrice(subtotal) }}</span>
              </div>
              <div v-if="discount > 0" class="flex justify-between text-green-600">
                <span>Discount</span>
                <span class="font-semibold">−{{ currencySymbol }}{{ formatPrice(discount) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Shipping</span>
                <span :class="shipping === 0 ? 'text-green-600 font-semibold' : 'font-semibold text-gray-900'">
                  {{ shipping === 0 ? 'FREE' : `${currencySymbol}${formatPrice(shipping)}` }}
                </span>
              </div>
              <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-base text-gray-900">
                <span>Total</span>
                <span>{{ currencySymbol }}{{ formatPrice(total) }}</span>
              </div>
            </div>

            <!-- Free Shipping Progress -->
            <div v-if="freeShippingThreshold && subtotal < freeShippingThreshold" class="mb-5">
              <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                <span>Free shipping progress</span>
                <span class="font-semibold text-indigo-600">{{ currencySymbol }}{{ formatPrice(freeShippingThreshold - subtotal) }} away</span>
              </div>
              <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                <div
                  class="h-full bg-indigo-600 rounded-full transition-all duration-500"
                  :style="{ width: `${Math.min(100, (subtotal / freeShippingThreshold) * 100)}%` }"
                ></div>
              </div>
              <p class="text-xs text-gray-500 mt-1.5">
                Add <span class="font-semibold text-indigo-600">{{ currencySymbol }}{{ formatPrice(freeShippingThreshold - subtotal) }}</span> more for free shipping!
              </p>
            </div>
            <div v-else-if="freeShippingThreshold" class="mb-5 p-3 bg-green-50 rounded-xl text-xs text-green-700 font-medium flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              You qualify for free shipping!
            </div>

            <!-- Checkout Button -->
            <Link
              href="/checkout"
              class="block w-full py-3.5 bg-indigo-600 text-white text-center font-bold rounded-xl hover:bg-indigo-700 transition-colors text-base"
            >
              Proceed to Checkout
              <svg class="w-4 h-4 inline-block ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </Link>

            <!-- Trust badges -->
            <div class="mt-4 flex items-center justify-center gap-4 text-xs text-gray-400">
              <div class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                SSL Secure
              </div>
              <div class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Free Returns
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../Layouts/ShopLayout.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  items: { type: Array, default: () => [] },
  subtotal: { type: Number, default: 0 },
  discount: { type: Number, default: 0 },
  shipping: { type: Number, default: 0 },
  total: { type: Number, default: 0 },
  couponCode: { type: String, default: '' },
  freeShippingThreshold: { type: Number, default: 100 }
})

const page = usePage()
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const couponInput = ref('')
const couponError = ref('')
const applyingCoupon = ref(false)
const updatingId = ref(null)

function formatPrice(price) {
  return Number(price || 0).toFixed(2)
}

function updateQuantity(item, newQty) {
  if (newQty < 1) return
  updatingId.value = item.id
  router.patch(`/cart/${item.id}`, { quantity: newQty }, {
    preserveScroll: true,
    onFinish: () => { updatingId.value = null }
  })
}

function removeItem(item) {
  router.delete(`/cart/${item.id}`, { preserveScroll: true })
}

function clearCart() {
  if (confirm('Are you sure you want to clear your cart?')) {
    router.delete('/cart', { preserveScroll: true })
  }
}

function applyCoupon() {
  if (!couponInput.value.trim()) return
  applyingCoupon.value = true
  couponError.value = ''
  router.post('/cart/coupon', { code: couponInput.value.trim() }, {
    preserveScroll: true,
    onSuccess: () => { couponInput.value = '' },
    onError: (errors) => { couponError.value = errors.code || 'Invalid coupon code' },
    onFinish: () => { applyingCoupon.value = false }
  })
}

function removeCoupon() {
  router.delete('/cart/coupon', { preserveScroll: true })
}
</script>
