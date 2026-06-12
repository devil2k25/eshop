<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  categories: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  filters: { type: Object, default: () => ({}) },
})

const search = ref(props.filters.search ?? '')
const deleteId = ref(null)
const deleteTarget = ref(null)

function applySearch() {
  router.get(route('admin.categories.index'), { search: search.value }, { preserveState: true, replace: true })
}

function clearSearch() {
  search.value = ''
  applySearch()
}

function confirmDelete(cat) {
  deleteTarget.value = cat
  deleteId.value = cat.id
}

function cancelDelete() {
  deleteId.value = null
  deleteTarget.value = null
}

function executeDelete() {
  router.delete(route('admin.categories.destroy', deleteId.value), {
    onSuccess: () => cancelDelete(),
  })
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <p class="text-sm text-gray-400 mt-0.5">Organize your products with categories</p>
      </div>
      <Link
        :href="route('admin.categories.create')"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Category
      </Link>
    </div>

    <!-- Search Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex gap-3">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Search categories..."
            class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
            @keyup.enter="applySearch"
            @input="applySearch"
          />
        </div>
        <button
          @click="applySearch"
          class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700 transition"
        >
          Search
        </button>
        <button
          v-if="search"
          @click="clearSearch"
          class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 transition"
        >
          Clear
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-5 py-3.5 font-medium">Name</th>
              <th class="px-5 py-3.5 font-medium">Slug</th>
              <th class="px-5 py-3.5 font-medium">Parent Category</th>
              <th class="px-5 py-3.5 font-medium">Status</th>
              <th class="px-5 py-3.5 font-medium">Sort Order</th>
              <th class="px-5 py-3.5 font-medium">Created At</th>
              <th class="px-5 py-3.5 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="cat in categories.data"
              :key="cat.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                    <img
                      v-if="cat.image"
                      :src="cat.image"
                      :alt="cat.name"
                      class="w-full h-full object-cover"
                    />
                    <svg v-else class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                  </div>
                  <span class="font-medium text-gray-800">{{ cat.name }}</span>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ cat.slug }}</span>
              </td>
              <td class="px-5 py-3.5 text-gray-600">{{ cat.parent?.name ?? '—' }}</td>
              <td class="px-5 py-3.5">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                >
                  {{ cat.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-5 py-3.5 text-gray-500">{{ cat.sort_order ?? 0 }}</td>
              <td class="px-5 py-3.5 text-gray-400 text-xs">
                {{ cat.created_at ? new Date(cat.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—' }}
              </td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-1.5">
                  <Link
                    :href="route('admin.categories.edit', cat.id)"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-lg hover:bg-indigo-50 transition"
                  >
                    Edit
                  </Link>
                  <button
                    @click="confirmDelete(cat)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2.5 py-1 rounded-lg hover:bg-red-50 transition"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="!categories.data?.length">
              <td colspan="7" class="px-5 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <p class="text-gray-400 font-medium">No categories found</p>
                <p class="text-gray-300 text-sm mt-1">Try adjusting your search or add a new category.</p>
                <Link
                  :href="route('admin.categories.create')"
                  class="inline-flex items-center gap-1 mt-4 text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >
                  Add your first category
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="categories.last_page > 1"
        class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm"
      >
        <p class="text-gray-400 text-xs">
          Showing {{ categories.from }}–{{ categories.to }} of {{ categories.total }} categories
        </p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in categories.links"
            :key="link.label"
            :href="link.url ?? '#'"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-medium transition',
              link.active
                ? 'bg-indigo-600 text-white'
                : link.url
                  ? 'text-gray-600 hover:bg-gray-100'
                  : 'text-gray-300 cursor-not-allowed pointer-events-none',
            ]"
            v-html="link.label"
          />
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <div
        v-if="deleteId"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
        @click.self="cancelDelete"
      >
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full">
          <div class="flex items-center gap-4 mb-4">
            <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-gray-900">Delete Category</h3>
              <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
          </div>
          <p class="text-sm text-gray-600 mb-6 pl-14">
            Are you sure you want to delete <strong class="text-gray-900">{{ deleteTarget?.name }}</strong>?
            Products in this category will not be deleted.
          </p>
          <div class="flex gap-3">
            <button
              @click="cancelDelete"
              class="flex-1 border border-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition"
            >
              Cancel
            </button>
            <button
              @click="executeDelete"
              class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl text-sm font-medium transition"
            >
              Delete Category
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
