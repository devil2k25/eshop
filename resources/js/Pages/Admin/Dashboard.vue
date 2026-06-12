<template>
  <AdminLayout title="Dashboard">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-purple-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-50 rounded-bl-full opacity-60" />
        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">💰</span>
          <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Revenue</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">${{ formatNumber(stats.total_revenue ?? 0) }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Revenue</p>
        <div class="mt-3 h-1 bg-purple-100 rounded-full">
          <div class="h-1 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full" style="width:75%" />
        </div>
      </div>

      <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-bl-full opacity-60" />
        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">🛒</span>
          <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Orders</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ formatNumber(stats.total_orders ?? 0) }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Orders</p>
        <div class="mt-3 h-1 bg-blue-100 rounded-full">
          <div class="h-1 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full" style="width:60%" />
        </div>
      </div>

      <div class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-bl-full opacity-60" />
        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">📦</span>
          <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Products</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ formatNumber(stats.total_products ?? 0) }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Products</p>
        <div class="mt-3 h-1 bg-emerald-100 rounded-full">
          <div class="h-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full" style="width:85%" />
        </div>
      </div>

      <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-50 rounded-bl-full opacity-60" />
        <div class="flex items-center justify-between mb-3">
          <span class="text-2xl">👥</span>
          <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Customers</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ formatNumber(stats.total_customers ?? 0) }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Customers</p>
        <div class="mt-3 h-1 bg-orange-100 rounded-full">
          <div class="h-1 bg-gradient-to-r from-orange-500 to-amber-500 rounded-full" style="width:50%" />
        </div>
      </div>
    </div>

    <!-- Alert Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-lg flex-shrink-0">🕐</div>
        <div>
          <p class="text-xl font-bold text-yellow-700">{{ stats.pending_orders ?? 0 }}</p>
          <p class="text-sm text-yellow-600">Pending Orders</p>
        </div>
        <Link href="/admin/orders?status=pending" class="ml-auto text-xs text-yellow-600 hover:text-yellow-800 font-medium underline">View</Link>
      </div>
      <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-lg flex-shrink-0">⚠️</div>
        <div>
          <p class="text-xl font-bold text-red-700">{{ stats.low_stock_products ?? 0 }}</p>
          <p class="text-sm text-red-600">Low Stock Products</p>
        </div>
        <Link href="/admin/products?status=low_stock" class="ml-auto text-xs text-red-600 hover:text-red-800 font-medium underline">View</Link>
      </div>
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-lg flex-shrink-0">💬</div>
        <div>
          <p class="text-xl font-bold text-blue-700">{{ stats.pending_reviews ?? 0 }}</p>
          <p class="text-sm text-blue-600">Pending Reviews</p>
        </div>
        <Link href="/admin/reviews?status=pending" class="ml-auto text-xs text-blue-600 hover:text-blue-800 font-medium underline">View</Link>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
      <!-- Recent Orders -->
      <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 class="font-semibold text-gray-800">Recent Orders</h2>
          <Link href="/admin/orders" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</Link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
                <th class="px-6 py-3 font-medium">Order #</th>
                <th class="px-6 py-3 font-medium">Customer</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Total</th>
                <th class="px-6 py-3 font-medium">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="order in recentOrders"
                :key="order.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-3">
                  <Link :href="`/admin/orders/${order.id}`" class="font-medium text-indigo-600 hover:text-indigo-800">
                    #{{ order.order_number }}
                  </Link>
                </td>
                <td class="px-6 py-3 text-gray-700">{{ order.customer_name }}</td>
                <td class="px-6 py-3">
                  <span :class="statusBadge(order.status)">{{ order.status }}</span>
                </td>
                <td class="px-6 py-3 font-medium text-gray-800">${{ order.total }}</td>
                <td class="px-6 py-3 text-gray-400">{{ formatDate(order.created_at) }}</td>
              </tr>
              <tr v-if="!recentOrders?.length">
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">No recent orders</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Products -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <h2 class="font-semibold text-gray-800">Top Products</h2>
          <Link href="/admin/products" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All →</Link>
        </div>
        <div class="p-4 space-y-3">
          <div
            v-for="(product, idx) in topProducts"
            :key="product.id"
            class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50"
          >
            <span class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-xs flex items-center justify-center font-bold flex-shrink-0">
              {{ idx + 1 }}
            </span>
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
              <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover" />
              <span v-else class="w-full h-full flex items-center justify-center text-gray-300 text-xl">📦</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-800 truncate">{{ product.name }}</p>
              <p class="text-xs text-gray-400">{{ product.sold }} sold</p>
            </div>
            <span class="text-sm font-semibold text-gray-700">${{ product.revenue }}</span>
          </div>
          <p v-if="!topProducts?.length" class="text-center text-gray-400 py-4 text-sm">No data</p>
        </div>
      </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h2 class="font-semibold text-gray-800 mb-6">Monthly Revenue</h2>
      <div class="flex items-end gap-2 h-40">
        <template v-for="(month, idx) in monthlyRevenue" :key="idx">
          <div class="flex-1 flex flex-col items-center gap-1 group">
            <span class="text-xs font-semibold text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity">
              ${{ month.revenue }}
            </span>
            <div
              class="w-full bg-gradient-to-t from-indigo-600 to-purple-500 rounded-t-lg transition-all duration-500"
              :style="{ height: barHeight(month.revenue) + '%' }"
            />
            <span class="text-xs text-gray-400 mt-1">{{ month.month }}</span>
          </div>
        </template>
        <p v-if="!monthlyRevenue?.length" class="text-gray-400 text-sm w-full text-center">No data</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  recentOrders: { type: Array, default: () => [] },
  monthlyRevenue: { type: Array, default: () => [] },
  topProducts: { type: Array, default: () => [] },
})

function formatNumber(n) {
  return Number(n).toLocaleString()
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

function statusBadge(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    processing: 'bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    shipped: 'bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    delivered: 'bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
    cancelled: 'bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize',
  }
  return map[status] ?? 'bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize'
}

const maxRevenue = computed(() => {
  if (!props.monthlyRevenue?.length) return 1
  return Math.max(...props.monthlyRevenue.map(m => Number(m.revenue) || 0), 1)
})

function barHeight(revenue) {
  const pct = (Number(revenue) / maxRevenue.value) * 100
  return Math.max(pct, 4)
}
</script>
