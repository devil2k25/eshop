<template>
  <AdminLayout title="Products">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Products</h2>
        <p class="text-sm text-gray-400">Manage your product catalog</p>
      </div>
      <Link
        href="/admin/products/create"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:opacity-90 transition shadow-sm"
      >
        <span>+</span> Add Product
      </Link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
      <div class="flex flex-col sm:flex-row gap-3">
        <input
          v-model="filterForm.search"
          type="text"
          placeholder="Search products..."
          class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
          @keyup.enter="applyFilters"
        />
        <select
          v-model="filterForm.category_id"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select
          v-model="filterForm.brand_id"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Brands</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
        </select>
        <select
          v-model="filterForm.status"
          class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="low_stock">Low Stock</option>
          <option value="deleted">Deleted</option>
        </select>
        <button
          @click="applyFilters"
          class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700"
        >Search</button>
        <button
          @click="clearFilters"
          class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200"
        >Clear</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr class="text-left text-xs text-gray-400 uppercase tracking-wider">
              <th class="px-4 py-3 font-medium">Image</th>
              <th class="px-4 py-3 font-medium">Name / SKU</th>
              <th class="px-4 py-3 font-medium">Category</th>
              <th class="px-4 py-3 font-medium">Brand</th>
              <th class="px-4 py-3 font-medium">Price</th>
              <th class="px-4 py-3 font-medium">Stock</th>
              <th class="px-4 py-3 font-medium">Status</th>
              <th class="px-4 py-3 font-medium">Created</th>
              <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="product in products.data"
              :key="product.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">
                <div class="w-12 h-12 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                  <img
                    v-if="product.primary_image"
                    :src="product.primary_image"
                    :alt="product.name"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="w-full h-full flex items-center justify-center text-gray-300 text-xl">📦</span>
                </div>
              </td>
              <td class="px-4 py-3">
                <p class="font-medium text-gray-800">{{ product.name }}</p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ product.sku }}</p>
              </td>
              <td class="px-4 py-3 text-gray-600">{{ product.category?.name ?? '—' }}</td>
              <td class="px-4 py-3 text-gray-600">{{ product.brand?.name ?? '—' }}</td>
              <td class="px-4 py-3">
                <p class="font-semibold text-gray-800">${{ product.price }}</p>
                <p v-if="product.compare_price" class="text-xs text-gray-400 line-through">${{ product.compare_price }}</p>
              </td>
              <td class="px-4 py-3">
                <span
                  :class="[
                    'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium',
                    product.stock <= (product.low_stock_threshold ?? 5)
                      ? 'bg-red-100 text-red-700'
                      : 'bg-gray-100 text-gray-700'
                  ]"
                >
                  <span v-if="product.stock <= (product.low_stock_threshold ?? 5)">⚠️</span>
                  {{ product.stock }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  v-if="product.deleted_at"
                  class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium"
                >Deleted</span>
                <span
                  v-else-if="product.is_active"
                  class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-xs font-medium"
                >Active</span>
                <span
                  v-else
                  class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium"
                >Inactive</span>
              </td>
              <td class="px-4 py-3 text-gray-400 text-xs">{{ formatDate(product.created_at) }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="`/admin/products/${product.id}/edit`"
                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-indigo-50"
                  >Edit</Link>
                  <button
                    v-if="product.deleted_at"
                    @click="restoreProduct(product.id)"
                    class="text-emerald-600 hover:text-emerald-800 text-xs font-medium px-2 py-1 rounded-lg hover:bg-emerald-50"
                  >Restore</button>
                  <button
                    v-else
                    @click="confirmDelete(product)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded-lg hover:bg-red-50"
                  >Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!products.data?.length">
              <td colspan="9" class="px-4 py-12 text-center text-gray-400">No products found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="px-4 py-4 border-t border-gray-100 flex items-center justify-between text-sm">
        <p class="text-gray-400">
          Showing {{ products.from }}–{{ products.to }} of {{ products.total }}
        </p>
        <div class="flex items-center gap-1">
          <Link
            v-for="link in products.links"
            :key="link.label"
            :href="link.url ?? '#'"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-medium transition',
              link.active
                ? 'bg-indigo-600 text-white'
                : link.url
                  ? 'text-gray-600 hover:bg-gray-100'
                  : 'text-gray-300 cursor-not-allowed pointer-events-none'
            ]"
            v-html="link.label"
          />
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="deleteModal.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4">
      <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full">
        <div class="text-center">
          <span class="text-4xl">🗑️</span>
          <h3 class="text-lg font-semibold text-gray-800 mt-3 mb-1">Delete Product?</h3>
          <p class="text-sm text-gray-500 mb-6">
            Are you sure you want to delete <strong>{{ deleteModal.product?.name }}</strong>? This action can be undone.
          </p>
          <div class="flex gap-3">
            <button
              @click="deleteModal.show = false"
              class="flex-1 border border-gray-200 text-gray-700 py-2 rounded-xl text-sm font-medium hover:bg-gray-50"
            >Cancel</button>
            <button
              @click="deleteProduct"
              class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-medium hover:bg-red-600"
            >Delete</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  products: { type: Object, default: () => ({ data: [], links: [], last_page: 1 }) },
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
})

const filterForm = reactive({
  search: props.filters.search ?? '',
  category_id: props.filters.category_id ?? '',
  brand_id: props.filters.brand_id ?? '',
  status: props.filters.status ?? '',
})

const deleteModal = reactive({ show: false, product: null })

function applyFilters() {
  router.get('/admin/products', filterForm, { preserveState: true, replace: true })
}

function clearFilters() {
  filterForm.search = ''
  filterForm.category_id = ''
  filterForm.brand_id = ''
  filterForm.status = ''
  applyFilters()
}

function confirmDelete(product) {
  deleteModal.product = product
  deleteModal.show = true
}

function deleteProduct() {
  router.delete(`/admin/products/${deleteModal.product.id}`, {
    onSuccess: () => { deleteModal.show = false }
  })
}

function restoreProduct(id) {
  router.post(`/admin/products/${id}/restore`)
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>
