<template>
  <AdminLayout title="Customers">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Customers</h2>
        <p class="text-sm text-gray-400">Manage your customer accounts</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex gap-3 flex-wrap">
        <input
          v-model="filterForm.search"
          type="text"
          placeholder="Search name or email..."
          class="flex-1 min-w-0 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
          @keyup.enter="applyFilters"
        />
        <select
          v-model="filterForm.status"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <button @click="applyFilters" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700">Search</button>
        <button @click="clearFilters" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200">Clear</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3 font-medium">Customer</th>
              <th class="px-5 py-3 font-medium">Email</th>
              <th class="px-5 py-3 font-medium">Orders</th>
              <th class="px-5 py-3 font-medium">Total Spent</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium">Joined</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="customer in customers.data"
              :key="customer.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ getInitials(customer.name) }}
                  </div>
                  <span class="font-medium text-gray-800">{{ customer.name }}</span>
                </div>
              </td>
              <td class="px-5 py-3 text-gray-500">{{ customer.email }}</td>
              <td class="px-5 py-3">
                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">
                  {{ customer.orders_count ?? 0 }}
                </span>
              </td>
              <td class="px-5 py-3 font-semibold text-gray-800">${{ customer.total_spent ?? '0.00' }}</td>
              <td class="px-5 py-3">
                <button
                  @click="toggleActive(customer)"
                  :class="[
                    'px-2 py-0.5 rounded-full text-xs font-medium transition',
                    customer.is_active
                      ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  ]"
                >{{ customer.is_active ? 'Active' : 'Inactive' }}</button>
              </td>
              <td class="px-5 py-3 text-gray-400 text-xs">{{ formatDate(customer.created_at) }}</td>
              <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/customers/${customer.id}`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >View</Link>
                </div>
              </td>
            </tr>
            <tr v-if="!customers.data?.length">
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">No customers found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="customers.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ customers.from }}–{{ customers.to }} of {{ customers.total }}</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in customers.links"
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
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  customers: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  filters: { type: Object, default: () => ({}) },
})

const filterForm = reactive({
  search: props.filters.search ?? '',
  status: props.filters.status ?? '',
})

function applyFilters() {
  router.get('/admin/customers', filterForm, { preserveState: true, replace: true })
}

function clearFilters() {
  Object.assign(filterForm, { search: '', status: '' })
  applyFilters()
}

function toggleActive(customer) {
  router.patch(`/admin/customers/${customer.id}/toggle-active`, {}, { preserveScroll: true })
}

function getInitials(name) {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>
