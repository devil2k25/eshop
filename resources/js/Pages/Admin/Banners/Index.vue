<template>
  <AdminLayout title="Banners">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Banners</h2>
        <p class="text-sm text-gray-400">Manage promotional banners</p>
      </div>
      <Link
        href="/admin/banners/create"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:opacity-90 transition shadow-sm"
      >
        + Add Banner
      </Link>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3 font-medium">Image</th>
              <th class="px-5 py-3 font-medium">Title</th>
              <th class="px-5 py-3 font-medium">Position</th>
              <th class="px-5 py-3 font-medium">Sort</th>
              <th class="px-5 py-3 font-medium">Dates</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="banner in banners.data"
              :key="banner.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <div class="w-20 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                  <img
                    v-if="banner.image"
                    :src="banner.image"
                    :alt="banner.title"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="w-full h-full flex items-center justify-center text-gray-300 text-xl">🖼️</span>
                </div>
              </td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ banner.title }}</p>
                <p v-if="banner.subtitle" class="text-xs text-gray-400 truncate max-w-xs">{{ banner.subtitle }}</p>
                <a
                  v-if="banner.button_link"
                  :href="banner.button_link"
                  target="_blank"
                  class="text-xs text-indigo-500 hover:text-indigo-700 truncate block max-w-xs"
                >{{ banner.button_link }}</a>
              </td>
              <td class="px-5 py-3">
                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-xs font-medium capitalize">
                  {{ banner.position }}
                </span>
              </td>
              <td class="px-5 py-3 text-gray-500">{{ banner.sort_order }}</td>
              <td class="px-5 py-3 text-xs text-gray-500 space-y-0.5">
                <div v-if="banner.starts_at">
                  <span class="text-gray-400">From:</span> {{ formatDate(banner.starts_at) }}
                </div>
                <div v-if="banner.ends_at">
                  <span class="text-gray-400">To:</span>
                  <span :class="isExpired(banner.ends_at) ? 'text-red-500' : ''">{{ formatDate(banner.ends_at) }}</span>
                </div>
                <span v-if="!banner.starts_at && !banner.ends_at" class="text-gray-300">—</span>
              </td>
              <td class="px-5 py-3">
                <span
                  :class="banner.is_active && !isExpired(banner.ends_at)
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >{{ banner.is_active && !isExpired(banner.ends_at) ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/banners/${banner.id}/edit`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >Edit</Link>
                  <button
                    @click="confirmDelete(banner)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded-lg hover:bg-red-50"
                  >Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!banners.data?.length">
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">No banners found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="banners.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ banners.from }}–{{ banners.to }} of {{ banners.total }}</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in banners.links"
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
        <span class="text-4xl">🖼️</span>
        <h3 class="text-lg font-semibold text-gray-800 mt-3 mb-1">Delete Banner?</h3>
        <p class="text-sm text-gray-500 mb-6">Delete <strong>{{ deleteModal.banner?.title }}</strong>?</p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false" class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-xl text-sm font-medium hover:bg-gray-50">Cancel</button>
          <button @click="deleteBanner" class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-medium hover:bg-red-600">Delete</button>
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
  banners: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
})

const deleteModal = reactive({ show: false, banner: null })

function confirmDelete(banner) {
  deleteModal.banner = banner
  deleteModal.show = true
}

function deleteBanner() {
  router.delete(`/admin/banners/${deleteModal.banner.id}`, {
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
