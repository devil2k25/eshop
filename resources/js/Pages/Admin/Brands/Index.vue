<template>
  <AdminLayout title="Brands">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Brands</h2>
        <p class="text-sm text-gray-400">Manage product brands</p>
      </div>
      <Link
        href="/admin/brands/create"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:opacity-90 transition shadow-sm"
      >
        + Add Brand
      </Link>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex gap-3">
        <input
          v-model="search"
          type="text"
          placeholder="Search brands..."
          class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
          @keyup.enter="applySearch"
        />
        <button @click="applySearch" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700">Search</button>
        <button @click="clearSearch" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200">Clear</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3 font-medium">Logo</th>
              <th class="px-5 py-3 font-medium">Name</th>
              <th class="px-5 py-3 font-medium">Slug</th>
              <th class="px-5 py-3 font-medium">Products</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="brand in brands.data"
              :key="brand.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <div class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
                  <img v-if="brand.logo" :src="brand.logo" :alt="brand.name" class="w-10 h-10 object-contain" />
                  <span v-else class="text-2xl">🏷️</span>
                </div>
              </td>
              <td class="px-5 py-3">
                <p class="font-medium text-gray-800">{{ brand.name }}</p>
                <a
                  v-if="brand.website"
                  :href="brand.website"
                  target="_blank"
                  class="text-xs text-indigo-500 hover:text-indigo-700 truncate block max-w-xs"
                >{{ brand.website }}</a>
              </td>
              <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ brand.slug }}</td>
              <td class="px-5 py-3">
                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">
                  {{ brand.products_count ?? 0 }}
                </span>
              </td>
              <td class="px-5 py-3">
                <span
                  :class="brand.is_active
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >{{ brand.is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/brands/${brand.id}/edit`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >Edit</Link>
                  <button
                    @click="confirmDelete(brand)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded-lg hover:bg-red-50"
                  >Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!brands.data?.length">
              <td colspan="6" class="px-5 py-12 text-center text-gray-400">No brands found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="brands.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ brands.from }}–{{ brands.to }} of {{ brands.total }}</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in brands.links"
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
        <span class="text-4xl">🗑️</span>
        <h3 class="text-lg font-semibold text-gray-800 mt-3 mb-1">Delete Brand?</h3>
        <p class="text-sm text-gray-500 mb-6">Delete <strong>{{ deleteModal.brand?.name }}</strong>?</p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false" class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-xl text-sm font-medium hover:bg-gray-50">Cancel</button>
          <button @click="deleteBrand" class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-medium hover:bg-red-600">Delete</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  brands: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  filters: { type: Object, default: () => ({}) },
})

const search = ref(props.filters.search ?? '')
const deleteModal = reactive({ show: false, brand: null })

function applySearch() {
  router.get('/admin/brands', { search: search.value }, { preserveState: true, replace: true })
}

function clearSearch() {
  search.value = ''
  applySearch()
}

function confirmDelete(brand) {
  deleteModal.brand = brand
  deleteModal.show = true
}

function deleteBrand() {
  router.delete(`/admin/brands/${deleteModal.brand.id}`, {
    onSuccess: () => { deleteModal.show = false }
  })
}
</script>
