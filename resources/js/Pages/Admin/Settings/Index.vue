<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
    </div>

    <!-- Flash Success -->
    <div
      v-if="flashSuccess"
      class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6 text-sm"
    >
      <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      {{ flashSuccess }}
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="border-b border-gray-200">
        <div class="flex">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'flex items-center gap-2 px-6 py-3.5 text-sm font-medium border-b-2 transition-colors',
              activeTab === tab.key
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- General Tab -->
      <div v-if="activeTab === 'general'" class="p-6">
        <form @submit.prevent="saveGeneral">
          <div class="max-w-2xl space-y-5">
            <div>
              <h2 class="text-base font-semibold text-gray-900 mb-1">Store Information</h2>
              <p class="text-sm text-gray-500 mb-4">Basic details about your store.</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
              <input
                v-model="generalForm.store_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                :class="generalForm.errors.store_name ? 'border-red-300' : ''"
                placeholder="My Awesome Store"
              />
              <p v-if="generalForm.errors.store_name" class="mt-1 text-xs text-red-600">{{ generalForm.errors.store_name }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Email</label>
                <input
                  v-model="generalForm.store_email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :class="generalForm.errors.store_email ? 'border-red-300' : ''"
                  placeholder="hello@store.com"
                />
                <p v-if="generalForm.errors.store_email" class="mt-1 text-xs text-red-600">{{ generalForm.errors.store_email }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Phone</label>
                <input
                  v-model="generalForm.store_phone"
                  type="tel"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="+1 (555) 000-0000"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Store Address</label>
              <textarea
                v-model="generalForm.store_address"
                rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                placeholder="123 Main St, City, State 12345"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                <input
                  v-model="generalForm.currency"
                  type="text"
                  maxlength="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase"
                  placeholder="USD"
                  @input="generalForm.currency = generalForm.currency.toUpperCase()"
                />
                <p class="mt-1 text-xs text-gray-400">e.g. USD, EUR, GBP</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                <input
                  v-model="generalForm.currency_symbol"
                  type="text"
                  maxlength="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="$"
                />
                <p class="mt-1 text-xs text-gray-400">e.g. $, €, £</p>
              </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
              <button
                type="submit"
                :disabled="generalForm.processing"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors disabled:opacity-60 shadow-sm"
              >
                <svg v-if="generalForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ generalForm.processing ? 'Saving...' : 'Save General Settings' }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Shipping Tab -->
      <div v-if="activeTab === 'shipping'" class="p-6">
        <form @submit.prevent="saveShipping">
          <div class="max-w-2xl space-y-5">
            <div>
              <h2 class="text-base font-semibold text-gray-900 mb-1">Shipping Configuration</h2>
              <p class="text-sm text-gray-500 mb-4">Configure shipping costs and thresholds.</p>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-indigo-700">
                  Orders above the free shipping threshold will automatically have shipping costs waived.
                </p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Free Shipping Threshold ($)</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                <input
                  v-model="shippingForm.free_shipping_threshold"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :class="shippingForm.errors.free_shipping_threshold ? 'border-red-300' : ''"
                  placeholder="0.00"
                />
              </div>
              <p class="mt-1 text-xs text-gray-400">Set to 0 to disable free shipping</p>
              <p v-if="shippingForm.errors.free_shipping_threshold" class="mt-1 text-xs text-red-600">{{ shippingForm.errors.free_shipping_threshold }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Default Shipping Cost ($)</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                <input
                  v-model="shippingForm.default_shipping_cost"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :class="shippingForm.errors.default_shipping_cost ? 'border-red-300' : ''"
                  placeholder="0.00"
                />
              </div>
              <p v-if="shippingForm.errors.default_shipping_cost" class="mt-1 text-xs text-red-600">{{ shippingForm.errors.default_shipping_cost }}</p>
            </div>

            <div class="pt-4 border-t border-gray-100">
              <button
                type="submit"
                :disabled="shippingForm.processing"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors disabled:opacity-60 shadow-sm"
              >
                <svg v-if="shippingForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ shippingForm.processing ? 'Saving...' : 'Save Shipping Settings' }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Tax Tab -->
      <div v-if="activeTab === 'tax'" class="p-6">
        <form @submit.prevent="saveTax">
          <div class="max-w-2xl space-y-5">
            <div>
              <h2 class="text-base font-semibold text-gray-900 mb-1">Tax Configuration</h2>
              <p class="text-sm text-gray-500 mb-4">Configure how taxes are applied to orders.</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
              <div class="relative">
                <input
                  v-model="taxForm.tax_rate"
                  type="number"
                  min="0"
                  max="100"
                  step="0.01"
                  class="w-full pr-8 pl-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :class="taxForm.errors.tax_rate ? 'border-red-300' : ''"
                  placeholder="0.00"
                />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
              </div>
              <p class="mt-1 text-xs text-gray-400">e.g. 8.25 for 8.25% tax rate</p>
              <p v-if="taxForm.errors.tax_rate" class="mt-1 text-xs text-red-600">{{ taxForm.errors.tax_rate }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tax Inclusive Pricing</label>
              <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-xl">
                <button
                  type="button"
                  @click="taxForm.tax_inclusive = !taxForm.tax_inclusive"
                  :class="taxForm.tax_inclusive ? 'bg-indigo-600' : 'bg-gray-200'"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 flex-shrink-0 mt-0.5"
                >
                  <span
                    :class="taxForm.tax_inclusive ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"
                  />
                </button>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ taxForm.tax_inclusive ? 'Prices include tax' : 'Prices exclude tax' }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">
                    {{ taxForm.tax_inclusive
                      ? 'Tax is included in displayed product prices. No additional tax will be added at checkout.'
                      : 'Tax will be added on top of product prices at checkout.'
                    }}
                  </p>
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
              <button
                type="submit"
                :disabled="taxForm.processing"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors disabled:opacity-60 shadow-sm"
              >
                <svg v-if="taxForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ taxForm.processing ? 'Saving...' : 'Save Tax Settings' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, defineComponent, h } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({
      general: {},
      shipping: {},
      tax: {},
    }),
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success ?? null)

const activeTab = ref('general')

// Icon components inline
const StoreIcon = defineComponent({
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z' }),
  ]),
})

const ShippingIcon = defineComponent({
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' }),
  ]),
})

const TaxIcon = defineComponent({
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z' }),
  ]),
})

const tabs = [
  { key: 'general', label: 'General', icon: StoreIcon },
  { key: 'shipping', label: 'Shipping', icon: ShippingIcon },
  { key: 'tax', label: 'Tax', icon: TaxIcon },
]

// Forms
const generalForm = useForm({
  store_name: props.settings.general?.store_name ?? '',
  store_email: props.settings.general?.store_email ?? '',
  store_phone: props.settings.general?.store_phone ?? '',
  store_address: props.settings.general?.store_address ?? '',
  currency: props.settings.general?.currency ?? 'USD',
  currency_symbol: props.settings.general?.currency_symbol ?? '$',
})

const shippingForm = useForm({
  free_shipping_threshold: props.settings.shipping?.free_shipping_threshold ?? '',
  default_shipping_cost: props.settings.shipping?.default_shipping_cost ?? '',
})

const taxForm = useForm({
  tax_rate: props.settings.tax?.tax_rate ?? '',
  tax_inclusive: props.settings.tax?.tax_inclusive ?? false,
})

function saveGeneral() {
  generalForm.post(route('admin.settings.update', { group: 'general' }), {
    preserveScroll: true,
  })
}

function saveShipping() {
  shippingForm.post(route('admin.settings.update', { group: 'shipping' }), {
    preserveScroll: true,
  })
}

function saveTax() {
  taxForm.post(route('admin.settings.update', { group: 'tax' }), {
    preserveScroll: true,
  })
}
</script>
