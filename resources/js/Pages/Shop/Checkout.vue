<template>
  <div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-gray-900">Checkout</h1>
        <p class="text-gray-500 mt-1">Complete your order</p>
      </div>

      <!-- Checkout Steps Indicator -->
      <div class="flex items-center gap-2 mb-8">
        <div class="flex items-center gap-2">
          <span class="w-7 h-7 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center">1</span>
          <span class="text-sm font-semibold text-indigo-600">Shipping</span>
        </div>
        <div class="flex-1 h-0.5 bg-gray-200 max-w-16"></div>
        <div class="flex items-center gap-2">
          <span class="w-7 h-7 bg-gray-200 text-gray-600 text-xs font-bold rounded-full flex items-center justify-center">2</span>
          <span class="text-sm font-semibold text-gray-400">Payment</span>
        </div>
        <div class="flex-1 h-0.5 bg-gray-200 max-w-16"></div>
        <div class="flex items-center gap-2">
          <span class="w-7 h-7 bg-gray-200 text-gray-600 text-xs font-bold rounded-full flex items-center justify-center">3</span>
          <span class="text-sm font-semibold text-gray-400">Confirm</span>
        </div>
      </div>

      <form @submit.prevent="placeOrder">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
          <!-- Left: Shipping + Payment -->
          <div class="lg:col-span-3 space-y-6">
            <!-- Shipping Address -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-indigo-100 text-indigo-600 text-xs font-bold rounded-full flex items-center justify-center">1</span>
                Shipping Address
              </h2>

              <!-- Saved Addresses -->
              <div v-if="auth?.user && savedAddresses && savedAddresses.length" class="mb-5 space-y-3">
                <label
                  v-for="addr in savedAddresses"
                  :key="addr.id"
                  :class="[
                    'flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors',
                    form.address_id === addr.id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-100 hover:border-indigo-200'
                  ]"
                >
                  <input
                    type="radio"
                    :value="addr.id"
                    v-model="form.address_id"
                    @change="useNewAddress = false"
                    class="mt-0.5 text-indigo-600 focus:ring-indigo-500"
                  />
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="font-semibold text-gray-800 text-sm">{{ addr.name }}</span>
                      <span v-if="addr.is_default" class="px-2 py-0.5 bg-indigo-100 text-indigo-600 text-xs font-bold rounded-full">Default</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ addr.address_line1 }}<span v-if="addr.address_line2">, {{ addr.address_line2 }}</span></p>
                    <p class="text-xs text-gray-500">{{ addr.city }}, {{ addr.state }} {{ addr.zip_code }}</p>
                    <p class="text-xs text-gray-500">{{ addr.country }}</p>
                    <p class="text-xs text-gray-500">{{ addr.phone }}</p>
                  </div>
                </label>

                <label
                  :class="[
                    'flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors',
                    useNewAddress ? 'border-indigo-500 bg-indigo-50' : 'border-gray-100 hover:border-indigo-200'
                  ]"
                >
                  <input
                    type="radio"
                    v-model="useNewAddress"
                    :value="true"
                    @change="form.address_id = null"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span class="font-semibold text-sm text-gray-700">+ Add New Address</span>
                </label>
              </div>

              <!-- New Address Form -->
              <div v-if="!auth?.user || useNewAddress || !savedAddresses?.length" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name *</label>
                    <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="John Doe" />
                    <p v-if="form.errors.name" class="text-xs text-rose-600 mt-1">{{ form.errors.name }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number *</label>
                    <input v-model="form.phone" type="tel" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="+1 (555) 000-0000" />
                    <p v-if="form.errors.phone" class="text-xs text-rose-600 mt-1">{{ form.errors.phone }}</p>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Address Line 1 *</label>
                  <input v-model="form.address_line1" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="123 Main Street" />
                  <p v-if="form.errors.address_line1" class="text-xs text-rose-600 mt-1">{{ form.errors.address_line1 }}</p>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Address Line 2 <span class="text-gray-400 font-normal">(optional)</span></label>
                  <input v-model="form.address_line2" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="Apartment, suite, floor..." />
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">City *</label>
                    <input v-model="form.city" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="San Francisco" />
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">State *</label>
                    <input v-model="form.state" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="CA" />
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">ZIP Code *</label>
                    <input v-model="form.zip_code" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="94102" />
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Country *</label>
                  <select v-model="form.country" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                    <option value="">Select country</option>
                    <option value="US">United States</option>
                    <option value="CA">Canada</option>
                    <option value="GB">United Kingdom</option>
                    <option value="AU">Australia</option>
                    <option value="IN">India</option>
                    <option value="DE">Germany</option>
                    <option value="FR">France</option>
                    <option value="JP">Japan</option>
                  </select>
                  <p v-if="form.errors.country" class="text-xs text-rose-600 mt-1">{{ form.errors.country }}</p>
                </div>
                <div v-if="auth?.user">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="form.save_address" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <span class="text-sm text-gray-700">Save this address for future orders</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <h2 class="text-base font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-indigo-100 text-indigo-600 text-xs font-bold rounded-full flex items-center justify-center">2</span>
                Payment Method
              </h2>

              <div class="space-y-3">
                <!-- Cash on Delivery -->
                <label
                  :class="[
                    'flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-colors',
                    form.payment_method === 'cod' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-100 hover:border-indigo-200'
                  ]"
                >
                  <input type="radio" value="cod" v-model="form.payment_method" class="text-indigo-600 focus:ring-indigo-500" />
                  <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-800 text-sm">Cash on Delivery</p>
                      <p class="text-xs text-gray-500">Pay when your order arrives</p>
                    </div>
                  </div>
                  <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Available</span>
                </label>

                <!-- Credit Card (Coming Soon) -->
                <label
                  :class="[
                    'flex items-center gap-4 p-4 border-2 rounded-xl cursor-not-allowed opacity-60 border-gray-100'
                  ]"
                >
                  <input type="radio" value="card" disabled class="text-indigo-600 focus:ring-indigo-500" />
                  <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-600 text-sm">Credit / Debit Card</p>
                      <p class="text-xs text-gray-400">Visa, Mastercard, American Express</p>
                    </div>
                  </div>
                  <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Coming Soon</span>
                </label>

                <!-- PayPal (Coming Soon) -->
                <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-not-allowed opacity-60 border-gray-100">
                  <input type="radio" value="paypal" disabled class="text-indigo-600 focus:ring-indigo-500" />
                  <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                      <span class="text-blue-600 font-extrabold text-sm">PP</span>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-600 text-sm">PayPal</p>
                      <p class="text-xs text-gray-400">Fast, secure PayPal checkout</p>
                    </div>
                  </div>
                  <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Coming Soon</span>
                </label>
              </div>

              <!-- Order Notes -->
              <div class="mt-5">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Order Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea
                  v-model="form.notes"
                  rows="3"
                  placeholder="Special instructions for delivery, gift wrapping, etc."
                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Right: Order Summary -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
              <h2 class="text-base font-bold text-gray-900 mb-5">Your Order</h2>

              <!-- Items -->
              <div class="space-y-3 mb-5 max-h-64 overflow-y-auto pr-1">
                <div
                  v-for="item in cartItems"
                  :key="item.id"
                  class="flex items-center gap-3"
                >
                  <div class="relative flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gray-50 border border-gray-100">
                    <img :src="item.image || '/images/placeholder.png'" :alt="item.name" class="w-full h-full object-cover" />
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center">{{ item.quantity }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ item.name }}</p>
                    <p v-if="item.variant_label" class="text-xs text-gray-400">{{ item.variant_label }}</p>
                  </div>
                  <span class="text-xs font-bold text-gray-700 whitespace-nowrap">
                    {{ currencySymbol }}{{ formatPrice(item.price * item.quantity) }}
                  </span>
                </div>
              </div>

              <!-- Totals -->
              <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                  <span>Subtotal</span>
                  <span class="font-semibold">{{ currencySymbol }}{{ formatPrice(subtotal) }}</span>
                </div>
                <div v-if="discount > 0" class="flex justify-between text-green-600">
                  <span>Discount</span>
                  <span class="font-semibold">−{{ currencySymbol }}{{ formatPrice(discount) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                  <span>Shipping</span>
                  <span :class="shipping === 0 ? 'text-green-600 font-semibold' : 'font-semibold'">
                    {{ shipping === 0 ? 'FREE' : `${currencySymbol}${formatPrice(shipping)}` }}
                  </span>
                </div>
                <div v-if="tax > 0" class="flex justify-between text-gray-600">
                  <span>Tax</span>
                  <span class="font-semibold">{{ currencySymbol }}{{ formatPrice(tax) }}</span>
                </div>
                <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-base text-gray-900">
                  <span>Total</span>
                  <span>{{ currencySymbol }}{{ formatPrice(total) }}</span>
                </div>
              </div>

              <!-- Place Order Button -->
              <button
                type="submit"
                :disabled="form.processing"
                class="mt-5 w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 disabled:opacity-60 transition-colors flex items-center justify-center gap-2 text-base"
              >
                <svg v-if="!form.processing" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ form.processing ? 'Placing Order...' : 'Place Order' }}
              </button>

              <p class="text-xs text-gray-400 text-center mt-3">
                By placing your order, you agree to our
                <a href="#" class="text-indigo-500 hover:underline">Terms of Service</a>
              </p>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../Layouts/ShopLayout.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  cartItems: { type: Array, default: () => [] },
  savedAddresses: { type: Array, default: () => [] },
  subtotal: { type: Number, default: 0 },
  discount: { type: Number, default: 0 },
  shipping: { type: Number, default: 0 },
  tax: { type: Number, default: 0 },
  total: { type: Number, default: 0 }
})

const page = usePage()
const auth = computed(() => page.props.auth)
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const useNewAddress = ref(!props.savedAddresses?.length)

const form = useForm({
  // Address
  address_id: props.savedAddresses?.find(a => a.is_default)?.id || null,
  name: '',
  phone: '',
  address_line1: '',
  address_line2: '',
  city: '',
  state: '',
  zip_code: '',
  country: 'US',
  save_address: false,
  // Payment
  payment_method: 'cod',
  notes: ''
})

function formatPrice(price) {
  return Number(price || 0).toFixed(2)
}

function placeOrder() {
  form.post('/checkout', {
    onSuccess: () => form.reset()
  })
}
</script>
