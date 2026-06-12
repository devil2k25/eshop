<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Coupons</h1>
      <Link
        :href="route('admin.coupons.create')"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Coupon
      </Link>
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
            placeholder="Search coupons..."
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
          <option value="expired">Expired</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Code</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Type</th>
              <th class="text-right px-4 py-3 font-semibold text-gray-600">Value</th>
              <th class="text-right px-4 py-3 font-semibold text-gray-600">Min Order</th>
              <th class="text-center px-4 py-3 font-semibold text-gray-600">Usage</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Expiry</th>
              <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
              <th class="text-center px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="coupons.data.length === 0">
              <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <p>No coupons found</p>
              </td>
            </tr>
            <tr
              v-for="coupon in coupons.data"
              :key="coupon.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">
                <span class="font-mono font-semibold bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs tracking-wider">
                  {{ coupon.code }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-900 font-medium">{{ coupon.name }}</td>
              <td class="px-4 py-3 text-gray-600">
                <span :class="coupon.type === 'percentage' ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700'" class="px-2 py-0.5 rounded text-xs font-medium">
                  {{ coupon.type === 'percentage' ? 'Percentage' : 'Fixed' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right font-semibold text-gray-900">
                {{ coupon.type === 'percentage' ? coupon.value + '%' : formatCurrency(coupon.value) }}
              </td>
              <td class="px-4 py-3 text-right text-gray-600">
                {{ coupon.min_order_amount ? formatCurrency(coupon.min_order_amount) : '—' }}
              </td>
              <td class="px-4 py-3 text-center text-gray-600">
                <span v-if="coupon.usage_limit">{{ coupon.used_count ?? 0 }}/{{ coupon.usage_limit }}</span>
                <span v-else class="text-gray-400">{{ coupon.used_count ?? 0 }} / ∞</span>
              </td>
              <td class="px-4 py-3 text-gray-500 text-xs">
                {{ coupon.expires_at ? formatDate(coupon.expires_at) : '—' }}
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="couponStatusClass(coupon)" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold">
                  {{ couponStatusLabel(coupon) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <Link
                    :href="route('admin.coupons.edit', coupon.id)"
                    class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </Link>
                  <button
                    @click="confirmDelete(coupon)"
                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="coupons.links && coupons.links.length > 3" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">
          Showing {{ coupons.from }}–{{ coupons.to }} of {{ coupons.total }}
        </p>
        <div class="flex gap-1">
          <template v-for="link in coupons.links" :key="link.label">
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

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false" />
        <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-gray-900 text-lg">Delete Coupon</h3>
              <p class="text-gray-500 text-sm mt-1">
                Are you sure you want to delete coupon
                <span class="font-mono font-bold text-purple-700">{{ couponToDelete?.code }}</span>?
                This action cannot be undone.
              </p>
            </div>
          </div>
          <div class="flex gap-3 mt-6 justify-end">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="deleteCoupon"
              class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router, usePage } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  coupons: {
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
    router.get(route('admin.coupons.index'), filterForm, {
      preserveState: true,
      replace: true,
    })
  }, 350)
}

const showDeleteModal = ref(false)
const couponToDelete = ref(null)

function confirmDelete(coupon) {
  couponToDelete.value = coupon
  showDeleteModal.value = true
}

function deleteCoupon() {
  if (!couponToDelete.value) return
  router.delete(route('admin.coupons.destroy', couponToDelete.value.id), {
    onFinish: () => {
      showDeleteModal.value = false
      couponToDelete.value = null
    },
  })
}

function couponStatusLabel(coupon) {
  if (coupon.expires_at && new Date(coupon.expires_at) < new Date()) return 'Expired'
  return coupon.is_active ? 'Active' : 'Inactive'
}

function couponStatusClass(coupon) {
  if (coupon.expires_at && new Date(coupon.expires_at) < new Date()) return 'bg-gray-100 text-gray-600'
  return coupon.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}

function formatDate(date) {
  if (!date) return '—'
  return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(date))
}
</script>
