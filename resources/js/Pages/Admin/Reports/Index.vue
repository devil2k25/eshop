<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
        <p class="text-sm text-gray-400 mt-0.5">Last 12 months</p>
      </div>
      <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        {{ dateRangeLabel }}
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenue</span>
          <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(summary.total_revenue) }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Revenue</p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Orders</span>
          <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 16H4L5 9z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(summary.total_orders) }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Orders</p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Order</span>
          <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(summary.avg_order_value) }}</p>
        <p class="text-xs text-gray-400 mt-1">Avg Order Value</p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Customers</span>
          <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(summary.total_customers) }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Customers</p>
      </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <h2 class="font-semibold text-gray-900 mb-5">Monthly Revenue</h2>
      <div v-if="monthlyRevenue && monthlyRevenue.length" class="space-y-3">
        <div
          v-for="item in monthlyRevenue"
          :key="item.month"
          class="flex items-center gap-3"
        >
          <span class="text-xs text-gray-500 w-12 flex-shrink-0 text-right">{{ formatMonth(item.month) }}</span>
          <div class="flex-1 h-7 bg-gray-100 rounded-lg overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg flex items-center px-2 transition-all duration-500"
              :style="{ width: maxRevenue > 0 ? Math.max((item.revenue / maxRevenue) * 100, 2) + '%' : '2%' }"
            >
              <span v-if="(item.revenue / maxRevenue) > 0.18" class="text-xs text-white font-medium">
                {{ formatCurrency(item.revenue) }}
              </span>
            </div>
          </div>
          <span class="text-xs font-semibold text-gray-700 w-24 text-right flex-shrink-0">{{ formatCurrency(item.revenue) }}</span>
          <span class="text-xs text-gray-400 w-16 text-right flex-shrink-0">{{ item.count }} orders</span>
        </div>
      </div>
      <div v-else class="py-10 text-center text-gray-400 text-sm">No revenue data available.</div>
    </div>

    <!-- Two-column grid -->
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
      <!-- Top Selling Products -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Top Selling Products</h2>
        <div v-if="topProducts && topProducts.length" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="text-left pb-2 text-xs font-semibold text-gray-500">#</th>
                <th class="text-left pb-2 text-xs font-semibold text-gray-500">Product</th>
                <th class="text-right pb-2 text-xs font-semibold text-gray-500">Units</th>
                <th class="text-right pb-2 text-xs font-semibold text-gray-500">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="(product, index) in topProducts" :key="product.id" class="hover:bg-gray-50">
                <td class="py-2.5 pr-2">
                  <span
                    class="w-6 h-6 inline-flex items-center justify-center rounded-full text-xs font-bold"
                    :class="index === 0 ? 'bg-yellow-100 text-yellow-700' : index === 1 ? 'bg-gray-100 text-gray-600' : index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-400'"
                  >{{ index + 1 }}</span>
                </td>
                <td class="py-2.5">
                  <Link
                    :href="route('admin.products.show', product.id)"
                    class="font-medium text-gray-900 hover:text-indigo-600 transition-colors text-xs line-clamp-1"
                  >
                    {{ product.name }}
                  </Link>
                </td>
                <td class="py-2.5 text-right text-xs text-gray-600">{{ formatNumber(product.total_sold) }}</td>
                <td class="py-2.5 text-right text-xs font-semibold text-gray-900">{{ formatCurrency(product.revenue) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="py-8 text-center text-gray-400 text-sm">No product data available.</div>
      </div>

      <!-- Orders by Status -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Orders by Status</h2>
        <div v-if="ordersByStatus" class="space-y-3">
          <div v-for="statusItem in orderStatusList" :key="statusItem.key" class="flex items-center gap-3">
            <div :class="statusItem.dot" class="w-2.5 h-2.5 rounded-full flex-shrink-0" />
            <div class="flex-1">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-gray-700 capitalize">{{ statusItem.label }}</span>
                <span class="text-xs font-semibold text-gray-900">{{ ordersByStatus[statusItem.key] ?? 0 }}</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div
                  :class="statusItem.bar"
                  class="h-full rounded-full transition-all duration-500"
                  :style="{ width: totalOrders > 0 ? Math.max(((ordersByStatus[statusItem.key] ?? 0) / totalOrders) * 100, 0) + '%' : '0%' }"
                />
              </div>
            </div>
            <span class="text-xs text-gray-400 w-10 text-right flex-shrink-0">
              {{ totalOrders > 0 ? Math.round(((ordersByStatus[statusItem.key] ?? 0) / totalOrders) * 100) : 0 }}%
            </span>
          </div>
        </div>
        <div v-else class="py-8 text-center text-gray-400 text-sm">No order data available.</div>
      </div>
    </div>

    <!-- Revenue by Category -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <h2 class="font-semibold text-gray-900 mb-5">Revenue by Category</h2>
      <div v-if="revenueByCategory && revenueByCategory.length" class="space-y-4">
        <div
          v-for="cat in revenueByCategory"
          :key="cat.name"
          class="flex items-center gap-4"
        >
          <span class="text-sm text-gray-700 font-medium w-36 flex-shrink-0 truncate">{{ cat.name }}</span>
          <div class="flex-1 h-4 bg-gray-100 rounded-full overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-teal-500 to-cyan-400 rounded-full transition-all duration-500"
              :style="{ width: maxCategoryRevenue > 0 ? Math.max((cat.revenue / maxCategoryRevenue) * 100, 2) + '%' : '2%' }"
            />
          </div>
          <span class="text-sm font-semibold text-gray-900 w-24 text-right flex-shrink-0">{{ formatCurrency(cat.revenue) }}</span>
          <span class="text-xs text-gray-400 w-12 text-right flex-shrink-0">
            {{ totalCategoryRevenue > 0 ? Math.round((cat.revenue / totalCategoryRevenue) * 100) : 0 }}%
          </span>
        </div>
      </div>
      <div v-else class="py-8 text-center text-gray-400 text-sm">No category data available.</div>
    </div>
  </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  monthlyRevenue: {
    type: Array,
    default: () => [],
  },
  topProducts: {
    type: Array,
    default: () => [],
  },
  ordersByStatus: {
    type: Object,
    default: () => ({}),
  },
  revenueByCategory: {
    type: Array,
    default: () => [],
  },
  summary: {
    type: Object,
    default: () => ({
      total_revenue: 0,
      total_orders: 0,
      avg_order_value: 0,
      total_customers: 0,
    }),
  },
})

const orderStatusList = [
  { key: 'pending', label: 'Pending', dot: 'bg-yellow-400', bar: 'bg-yellow-400' },
  { key: 'processing', label: 'Processing', dot: 'bg-blue-500', bar: 'bg-blue-500' },
  { key: 'shipped', label: 'Shipped', dot: 'bg-purple-500', bar: 'bg-purple-500' },
  { key: 'delivered', label: 'Delivered', dot: 'bg-green-500', bar: 'bg-green-500' },
  { key: 'cancelled', label: 'Cancelled', dot: 'bg-red-400', bar: 'bg-red-400' },
  { key: 'refunded', label: 'Refunded', dot: 'bg-gray-400', bar: 'bg-gray-400' },
]

const maxRevenue = computed(() => {
  if (!props.monthlyRevenue || props.monthlyRevenue.length === 0) return 1
  return Math.max(...props.monthlyRevenue.map((m) => m.revenue || 0), 1)
})

const totalOrders = computed(() => {
  if (!props.ordersByStatus) return 0
  return Object.values(props.ordersByStatus).reduce((sum, v) => sum + (v || 0), 0)
})

const maxCategoryRevenue = computed(() => {
  if (!props.revenueByCategory || props.revenueByCategory.length === 0) return 1
  return Math.max(...props.revenueByCategory.map((c) => c.revenue || 0), 1)
})

const totalCategoryRevenue = computed(() => {
  if (!props.revenueByCategory) return 0
  return props.revenueByCategory.reduce((sum, c) => sum + (c.revenue || 0), 0)
})

const dateRangeLabel = computed(() => {
  const end = new Date()
  const start = new Date()
  start.setFullYear(start.getFullYear() - 1)
  const fmt = (d) => new Intl.DateTimeFormat('en-US', { month: 'short', year: 'numeric' }).format(d)
  return `${fmt(start)} – ${fmt(end)}`
})

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value || 0)
}

function formatNumber(value) {
  return new Intl.NumberFormat('en-US').format(value || 0)
}

function formatMonth(monthStr) {
  if (!monthStr) return ''
  // Handle 'YYYY-MM' or full date strings
  const parts = String(monthStr).split('-')
  if (parts.length >= 2) {
    const date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, 1)
    return new Intl.DateTimeFormat('en-US', { month: 'short', year: '2-digit' }).format(date)
  }
  return monthStr
}
</script>
