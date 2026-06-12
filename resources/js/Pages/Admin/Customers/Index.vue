<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
      <span class="text-sm text-gray-500">{{ customers.total }} total customers</span>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="filterForm.search"
            type="text"
            placeholder="Search by name or email..."
            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            @input="applyFilters"
          />
        </div>
        <select
          v-model="filterForm.status"
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
          @change="applyFilters"
        >
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Email</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Phone</th>
              <th class="text-right px-4 py-3 font-semibold text-gray-600">Orders</th>
              <th class="text-right px-4 py-3 font-semibold text-gray-600">Total Spent</th>
              <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Joined</th>
              <th class="text-center px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="customers.data.length === 0">
              <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p>No customers found</p>
              </td>
            </tr>
            <tr
              v-for="customer in customers.data"
              :key="customer.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-semibold text-indigo-700">{{ getInitials(customer.name) }}</span>
                  </div>
                  <span class="font-medium text-gray-900">{{ customer.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-gray-600">{{ customer.email }}</td>
              <td class="px-4 py-3 text-gray-600">{{ customer.phone || '—' }}</td>
              <td class="px-4 py-3 text-right font-medium text-gray-900">{{ customer.orders_count ?? 0 }}</td>
              <td class="px-4 py-3 text-right font-medium text-gray-900">{{ formatCurrency(customer.total_spent ?? 0) }}</td>
              <td class="px-4 py-3 text-center">
                <div class="flex flex-col items-center gap-1">
                  <span
                    :class="customer.is_active
                      ? 'bg-green-100 text-green-700'
                      : 'bg-red-100 text-red-700'"
                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                  >
                    {{ customer.is_active ? 'Active' : 'Inactive' }}
                  </span>
                  <button
                    @click="toggleActive(customer)"
                    :class="customer.is_active
                      ? 'text-red-600 hover:bg-red-50'
                      : 'text-green-600 hover:bg-green-50'"
                    class="text-xs px-2 py-0.5 rounded border border-current transition-colors"
                  >
                    {{ customer.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                </div>
              </td>
              <td class="px-4 py-3 text-gray-500 text-xs">{{ formatDate(customer.created_at) }}</td>
              <td class="px-4 py-3 text-center">
                <Link
                  :href="route('admin.customers.show', customer.id)"
                  class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium hover:bg-indigo-100 transition-colors"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  View
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="customers.links && customers.links.length > 3" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">
          Showing {{ customers.from }}–{{ customers.to }} of {{ customers.total }}
        </p>
        <div class="flex gap-1">
          <template v-for="link in customers.links" :key="link.label">
            <Link
              v-if="link.url"
              :href="link.url"
              v-html="link.label"
              :class="[
                'px-3 py-1.5 text-sm rounded-lg border transition-colors',
                link.active
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : 'border-gray-300 text-gray-600 hover:bg-gray-50'
              ]"
            />
            <span
              v-else
              v-html="link.label"
              class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-400 cursor-not-allowed"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  customers: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({ search: '', status: '' }),
  },
})

const filterForm = reactive({
  search: props.filters.search ?? '',
  status: props.filters.status ?? '',
})

let filterTimer = null

function applyFilters() {
  clearTimeout(filterTimer)
  filterTimer = setTimeout(() => {
    router.get(route('admin.customers.index'), filterForm, {
      preserveState: true,
      replace: true,
    })
  }, 350)
}

function toggleActive(customer) {
  router.patch(
    route('admin.customers.toggle-active', customer.id),
    {},
    { preserveScroll: true }
  )
}

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
</script>
