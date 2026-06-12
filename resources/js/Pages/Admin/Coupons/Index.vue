<template>
  <AdminLayout title="Coupons">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Coupons</h2>
        <p class="text-sm text-gray-400">Create and manage discount coupons</p>
      </div>
      <Link
        href="/admin/coupons/create"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:opacity-90 transition shadow-sm"
      >
        + Add Coupon
      </Link>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3 font-medium">Code</th>
              <th class="px-5 py-3 font-medium">Name</th>
              <th class="px-5 py-3 font-medium">Type</th>
              <th class="px-5 py-3 font-medium">Value</th>
              <th class="px-5 py-3 font-medium">Usage</th>
              <th class="px-5 py-3 font-medium">Min Order</th>
              <th class="px-5 py-3 font-medium">Expiry</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="coupon in coupons.data"
              :key="coupon.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <span class="font-mono bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg text-xs font-bold tracking-wider">
                  {{ coupon.code }}
                </span>
              </td>
              <td class="px-5 py-3 font-medium text-gray-800">{{ coupon.name }}</td>
              <td class="px-5 py-3">
                <span
                  :class="coupon.type === 'percentage' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                >{{ coupon.type }}</span>
              </td>
              <td class="px-5 py-3 font-semibold text-gray-800">
                {{ coupon.type === 'percentage' ? coupon.value + '%' : '$' + coupon.value }}
              </td>
              <td class="px-5 py-3">
                <span class="text-gray-600">{{ coupon.usage_count ?? 0 }}</span>
                <span v-if="coupon.usage_limit" class="text-gray-400"> / {{ coupon.usage_limit }}</span>
              </td>
              <td class="px-5 py-3 text-gray-600">
                {{ coupon.minimum_order ? '$' + coupon.minimum_order : '—' }}
              </td>
              <td class="px-5 py-3 text-xs">
                <span v-if="coupon.expires_at" :class="isExpired(coupon.expires_at) ? 'text-red-500 font-medium' : 'text-gray-500'">
                  {{ formatDate(coupon.expires_at) }}
                  <span v-if="isExpired(coupon.expires_at)" class="block text-xs opacity-70">Expired</span>
                </span>
                <span v-else class="text-gray-300">No expiry</span>
              </td>
              <td class="px-5 py-3">
                <span
                  :class="coupon.is_active && !isExpired(coupon.expires_at)
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >{{ coupon.is_active && !isExpired(coupon.expires_at) ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/coupons/${coupon.id}/edit`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >Edit</Link>
                  <button
                    @click="confirmDelete(coupon)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded-lg hover:bg-red-50"
                  >Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!coupons.data?.length">
              <td colspan="9" class="px-5 py-12 text-center text-gray-400">No coupons found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="coupons.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ coupons.from }}–{{ coupons.to }} of {{ coupons.total }}</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in coupons.links"
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

    <!-- Delete Modal -->
    <div v-if="deleteModal.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4">
      <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full text-center">
        <span class="text-4xl">🎟️</span>
        <h3 class="text-lg font-semibold text-gray-800 mt-3 mb-1">Delete Coupon?</h3>
        <p class="text-sm text-gray-500 mb-6">Delete coupon <strong class="font-mono">{{ deleteModal.coupon?.code }}</strong>?</p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false" class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-xl text-sm font-medium hover:bg-gray-50">Cancel</button>
          <button @click="deleteCoupon" class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-medium hover:bg-red-600">Delete</button>
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
  coupons: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
})

const deleteModal = reactive({ show: false, coupon: null })

function confirmDelete(coupon) {
  deleteModal.coupon = coupon
  deleteModal.show = true
}

function deleteCoupon() {
  router.delete(`/admin/coupons/${deleteModal.coupon.id}`, {
    onSuccess: () => { deleteModal.show = false }
  })
}

function isExpired(date) {
  if (!date) return false
  return new Date(date) < new Date()
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>
