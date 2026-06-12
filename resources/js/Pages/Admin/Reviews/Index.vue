<template>
  <AdminLayout title="Reviews">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Reviews</h2>
        <p class="text-sm text-gray-400">Moderate customer product reviews</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex gap-3 flex-wrap">
        <input
          v-model="filterForm.search"
          type="text"
          placeholder="Search by product or customer..."
          class="flex-1 min-w-0 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
          @keyup.enter="applyFilters"
        />
        <select
          v-model="filterForm.is_approved"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Status</option>
          <option value="1">Approved</option>
          <option value="0">Pending</option>
        </select>
        <select
          v-model="filterForm.rating"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Ratings</option>
          <option value="5">5 Stars</option>
          <option value="4">4 Stars</option>
          <option value="3">3 Stars</option>
          <option value="2">2 Stars</option>
          <option value="1">1 Star</option>
        </select>
        <button @click="applyFilters" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700">Filter</button>
        <button @click="clearFilters" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200">Clear</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3 font-medium">Product</th>
              <th class="px-5 py-3 font-medium">Customer</th>
              <th class="px-5 py-3 font-medium">Rating</th>
              <th class="px-5 py-3 font-medium">Title</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium">Verified</th>
              <th class="px-5 py-3 font-medium">Date</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="review in reviews.data"
              :key="review.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <Link
                  :href="`/admin/products/${review.product_id}/edit`"
                  class="font-medium text-indigo-600 hover:text-indigo-800 text-xs line-clamp-2 max-w-xs block"
                >{{ review.product_name }}</Link>
              </td>
              <td class="px-5 py-3 text-gray-700">{{ review.user_name }}</td>
              <td class="px-5 py-3">
                <div class="flex items-center gap-1">
                  <template v-for="i in 5" :key="i">
                    <svg
                      class="w-3.5 h-3.5"
                      :class="i <= review.rating ? 'text-amber-400' : 'text-gray-200'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </template>
                  <span class="text-xs text-gray-500 ml-1">({{ review.rating }})</span>
                </div>
              </td>
              <td class="px-5 py-3 text-gray-700 max-w-xs">
                <p class="font-medium truncate">{{ review.title }}</p>
                <p class="text-xs text-gray-400 truncate">{{ review.body }}</p>
              </td>
              <td class="px-5 py-3">
                <span
                  :class="review.is_approved
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-yellow-100 text-yellow-700'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >{{ review.is_approved ? 'Approved' : 'Pending' }}</span>
              </td>
              <td class="px-5 py-3">
                <span
                  :class="review.is_verified ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >{{ review.is_verified ? 'Verified' : 'Unverified' }}</span>
              </td>
              <td class="px-5 py-3 text-gray-400 text-xs">{{ formatDate(review.created_at) }}</td>
              <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="toggleApprove(review)"
                    :class="review.is_approved
                      ? 'text-yellow-600 hover:bg-yellow-50'
                      : 'text-emerald-600 hover:bg-emerald-50'"
                    class="text-xs font-medium px-2 py-1 rounded-lg transition"
                  >{{ review.is_approved ? 'Unapprove' : 'Approve' }}</button>
                  <button
                    @click="confirmDelete(review)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded-lg hover:bg-red-50"
                  >Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!reviews.data?.length">
              <td colspan="8" class="px-5 py-12 text-center text-gray-400">No reviews found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="reviews.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ reviews.from }}–{{ reviews.to }} of {{ reviews.total }}</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in reviews.links"
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
        <span class="text-4xl">⭐</span>
        <h3 class="text-lg font-semibold text-gray-800 mt-3 mb-1">Delete Review?</h3>
        <p class="text-sm text-gray-500 mb-6">This will permanently remove this review.</p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false" class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-xl text-sm font-medium hover:bg-gray-50">Cancel</button>
          <button @click="deleteReview" class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-medium hover:bg-red-600">Delete</button>
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
  reviews: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  filters: { type: Object, default: () => ({}) },
})

const filterForm = reactive({
  search: props.filters.search ?? '',
  is_approved: props.filters.is_approved ?? '',
  rating: props.filters.rating ?? '',
})

const deleteModal = reactive({ show: false, review: null })

function applyFilters() {
  router.get('/admin/reviews', filterForm, { preserveState: true, replace: true })
}

function clearFilters() {
  Object.assign(filterForm, { search: '', is_approved: '', rating: '' })
  applyFilters()
}

function toggleApprove(review) {
  router.patch(`/admin/reviews/${review.id}/toggle-approve`, {}, { preserveScroll: true })
}

function confirmDelete(review) {
  deleteModal.review = review
  deleteModal.show = true
}

function deleteReview() {
  router.delete(`/admin/reviews/${deleteModal.review.id}`, {
    onSuccess: () => { deleteModal.show = false }
  })
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>
