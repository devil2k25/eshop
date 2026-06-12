<template>
  <AdminLayout title="Categories">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Categories</h2>
        <p class="text-sm text-gray-400">Organize your products with categories</p>
      </div>
      <Link
        href="/admin/categories/create"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:opacity-90 transition shadow-sm"
      >
        + Add Category
      </Link>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex gap-3">
        <input
          v-model="search"
          type="text"
          placeholder="Search categories..."
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
              <th class="px-5 py-3 font-medium">Name</th>
              <th class="px-5 py-3 font-medium">Slug</th>
              <th class="px-5 py-3 font-medium">Parent</th>
              <th class="px-5 py-3 font-medium">Products</th>
              <th class="px-5 py-3 font-medium">Sort</th>
              <th class="px-5 py-3 font-medium">Status</th>
              <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="cat in categories.data"
              :key="cat.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    <img v-if="cat.image" :src="cat.image" :alt="cat.name" class="w-full h-full object-cover" />
                    <span v-else class="w-full h-full flex items-center justify-center text-gray-300 text-base">🗂️</span>
                  </div>
                  <span class="font-medium text-gray-800">{{ cat.name }}</span>
                </div>
              </td>
              <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ cat.slug }}</td>
              <td class="px-5 py-3 text-gray-600">{{ cat.parent?.name ?? '—' }}</td>
              <td class="px-5 py-3">
                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">
                  {{ cat.products_count ?? 0 }}
                </span>
              </td>
              <td class="px-5 py-3 text-gray-500">{{ cat.sort_order }}</td>
              <td class="px-5 py-3">
                <span
                  :class="cat.is_active
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >{{ cat.is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/categories/${cat.id}/edit`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >Edit</Link>
                  <button
                    @click="confirmDelete(cat)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded-lg hover:bg-red-50"
                  >Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!categories.data?.length">
              <td colspan="7" class="px-5 py-12 text-center text-gray-400">No categories found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="categories.last_page > 1" class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">Showing {{ categories.from }}–{{ categories.to }} of {{ categories.total }}</p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in categories.links"
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
        <h3 class="text-lg font-semibold text-gray-800 mt-3 mb-1">Delete Category?</h3>
        <p class="text-sm text-gray-500 mb-6">
          Delete <strong>{{ deleteModal.category?.name }}</strong>? Products in this category won't be deleted.
        </p>
        <div class="flex gap-3">
          <button @click="deleteModal.show = false" class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-xl text-sm font-medium hover:bg-gray-50">Cancel</button>
          <button @click="deleteCategory" class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-medium hover:bg-red-600">Delete</button>
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
  categories: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  filters: { type: Object, default: () => ({}) },
})

const search = ref(props.filters.search ?? '')
const deleteModal = reactive({ show: false, category: null })

function applySearch() {
  router.get('/admin/categories', { search: search.value }, { preserveState: true, replace: true })
}

function clearSearch() {
  search.value = ''
  applySearch()
}

function confirmDelete(cat) {
  deleteModal.category = cat
  deleteModal.show = true
}

function deleteCategory() {
  router.delete(`/admin/categories/${deleteModal.category.id}`, {
    onSuccess: () => { deleteModal.show = false }
  })
}
</script>
