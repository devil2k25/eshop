<template>
  <div>
    <!-- Back button -->
    <div class="mb-6">
      <Link
        :href="route('admin.customers.index')"
        class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-indigo-600 transition-colors"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Customers
      </Link>
    </div>

    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <!-- Avatar -->
        <div class="flex-shrink-0">
          <img
            v-if="customer.avatar"
            :src="customer.avatar"
            :alt="customer.name"
            class="w-20 h-20 rounded-full object-cover border-4 border-indigo-100"
          />
          <div
            v-else
            class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center border-4 border-indigo-100"
          >
            <span class="text-2xl font-bold text-white">{{ getInitials(customer.name) }}</span>
          </div>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <div class="flex flex-wrap items-center gap-3 mb-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ customer.name }}</h1>
            <span
              :class="customer.is_active
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700'"
              class="px-3 py-1 rounded-full text-xs font-semibold"
            >
              {{ customer.is_active ? 'Active' : 'Inactive' }}
            </span>
            <span v-if="customer.is_admin" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">
              Admin
            </span>
          </div>
          <p class="text-gray-500 text-sm">{{ customer.email }}</p>
          <p v-if="customer.phone" class="text-gray-500 text-sm">{{ customer.phone }}</p>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
        <div class="text-center">
          <p class="text-2xl font-bold text-indigo-600">{{ customer.orders?.length ?? 0 }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Total Orders</p>
        </div>
        <div class="text-center border-x border-gray-100">
          <p class="text-2xl font-bold text-indigo-600">{{ formatCurrency(totalSpent) }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Total Spent</p>
        </div>
        <div class="text-center">
          <p class="text-2xl font-bold text-indigo-600">{{ memberSince }}</p>
          <p class="text-xs text-gray-500 mt-0.5">Member Since</p>
        </div>
      </div>
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
              'px-6 py-3.5 text-sm font-medium border-b-2 transition-colors',
              activeTab === tab.key
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span
              :class="activeTab === tab.key ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'"
              class="ml-2 px-2 py-0.5 rounded-full text-xs"
            >
              {{ tab.key === 'orders' ? (customer.orders?.length ?? 0) : (customer.addresses?.length ?? 0) }}
            </span>
          </button>
        </div>
      </div>

      <!-- Orders Tab -->
      <div v-if="activeTab === 'orders'" class="p-0">
        <div v-if="!customer.orders || customer.orders.length === 0" class="py-16 text-center text-gray-400">
          <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z" />
          </svg>
          <p>No orders yet</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Order #</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                <th class="text-right px-4 py-3 font-semibold text-gray-600">Total</th>
                <th class="text-center px-4 py-3 font-semibold text-gray-600">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="order in customer.orders" :key="order.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 font-mono font-medium text-indigo-700">#{{ order.order_number ?? order.id }}</td>
                <td class="px-4 py-3 text-gray-600">{{ formatDate(order.created_at) }}</td>
                <td class="px-4 py-3">
                  <span :class="statusClass(order.status)" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold">
                    {{ capitalize(order.status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ formatCurrency(order.total) }}</td>
                <td class="px-4 py-3 text-center">
                  <Link
                    :href="route('admin.orders.show', order.id)"
                    class="text-xs px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors font-medium"
                  >
                    View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Addresses Tab -->
      <div v-if="activeTab === 'addresses'" class="p-6">
        <div v-if="!customer.addresses || customer.addresses.length === 0" class="py-10 text-center text-gray-400">
          <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <p>No addresses saved</p>
        </div>
        <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="address in customer.addresses"
            :key="address.id"
            class="border border-gray-200 rounded-xl p-4 relative hover:border-indigo-300 hover:shadow-sm transition-all"
          >
            <div v-if="address.is_default" class="absolute top-3 right-3">
              <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-0.5 rounded-full">Default</span>
            </div>
            <div class="mb-3">
              <p class="font-semibold text-gray-900">{{ address.name || customer.name }}</p>
              <p v-if="address.phone" class="text-sm text-gray-500">{{ address.phone }}</p>
            </div>
            <div class="text-sm text-gray-600 space-y-0.5">
              <p>{{ address.address_line1 }}</p>
              <p v-if="address.address_line2">{{ address.address_line2 }}</p>
              <p>{{ address.city }}<span v-if="address.state">, {{ address.state }}</span> {{ address.postal_code }}</p>
              <p v-if="address.country" class="text-gray-500">{{ address.country }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  customer: {
    type: Object,
    required: true,
  },
})

const activeTab = ref('orders')

const tabs = [
  { key: 'orders', label: 'Orders' },
  { key: 'addresses', label: 'Addresses' },
]

const totalSpent = computed(() => {
  if (!props.customer.orders) return 0
  return props.customer.orders.reduce((sum, o) => sum + (parseFloat(o.total) || 0), 0)
})

const memberSince = computed(() => {
  if (!props.customer.created_at) return '—'
  return new Intl.DateTimeFormat('en-US', { month: 'short', year: 'numeric' }).format(new Date(props.customer.created_at))
})

function getInitials(name) {
  if (!name) return '?'
  return name
    .split(' ')
    .slice(0, 2)
    .map((n) => n[0])
    .join('')
    .toUpperCase()
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}

function formatDate(date) {
  if (!date) return '—'
  return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(date))
}

function capitalize(str) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

function statusClass(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700',
    processing: 'bg-blue-100 text-blue-700',
    shipped: 'bg-purple-100 text-purple-700',
    delivered: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
    refunded: 'bg-gray-100 text-gray-700',
  }
  return map[status] ?? 'bg-gray-100 text-gray-600'
}
</script>
