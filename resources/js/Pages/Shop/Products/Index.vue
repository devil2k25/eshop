<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <Link href="/" class="hover:text-indigo-600 transition-colors">Home</Link>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span v-if="filters.category" class="hover:text-indigo-600 cursor-pointer" @click="clearFilter('category')">Products</span>
        <template v-if="filters.category">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          <span class="text-gray-800 font-medium capitalize">{{ filters.category }}</span>
        </template>
        <span v-else class="text-gray-800 font-medium">Products</span>
      </nav>

      <div class="flex gap-8">
        <!-- Mobile Filter Toggle -->
        <div class="lg:hidden mb-4 fixed bottom-4 left-1/2 -translate-x-1/2 z-30">
          <button
            @click="showMobileFilters = !showMobileFilters"
            class="flex items-center gap-2 px-5 py-3 bg-indigo-600 text-white rounded-full shadow-xl font-semibold text-sm"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filters
            <span v-if="activeFilterCount > 0" class="w-5 h-5 bg-white text-indigo-600 text-xs font-bold rounded-full flex items-center justify-center">{{ activeFilterCount }}</span>
          </button>
        </div>

        <!-- Sidebar Filters -->
        <aside
          :class="[
            'lg:block flex-shrink-0 lg:w-64',
            showMobileFilters
              ? 'fixed inset-0 z-40 flex lg:relative lg:inset-auto lg:z-auto'
              : 'hidden lg:flex'
          ]"
        >
          <!-- Mobile backdrop -->
          <div v-if="showMobileFilters" class="lg:hidden fixed inset-0 bg-black/40" @click="showMobileFilters = false"></div>

          <div class="relative bg-white lg:rounded-2xl border border-gray-100 shadow-sm overflow-y-auto h-full lg:h-auto w-72 lg:w-full p-5 space-y-6 lg:sticky lg:top-20">
            <!-- Header -->
            <div class="flex items-center justify-between">
              <h3 class="font-bold text-gray-900 text-base">Filters</h3>
              <div class="flex items-center gap-2">
                <button v-if="activeFilterCount > 0" @click="clearAllFilters" class="text-xs text-indigo-600 font-semibold hover:text-indigo-800">
                  Clear all ({{ activeFilterCount }})
                </button>
                <button class="lg:hidden p-1 text-gray-400 hover:text-gray-600" @click="showMobileFilters = false">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
            </div>

            <!-- Search -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Search</label>
              <div class="relative">
                <input
                  v-model="localFilters.search"
                  type="text"
                  placeholder="Search products..."
                  @input="debouncedApply"
                  class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent"
                />
                <svg class="absolute left-2.5 top-2.5 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </div>
            </div>

            <!-- Categories -->
            <div v-if="categories && categories.length">
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Categories</label>
              <div class="space-y-1">
                <button
                  @click="selectCategory('')"
                  :class="[
                    'w-full text-left px-3 py-2 text-sm rounded-lg transition-colors flex items-center justify-between',
                    !localFilters.category ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50'
                  ]"
                >
                  <span>All Categories</span>
                </button>
                <div v-for="cat in categories" :key="cat.id">
                  <button
                    @click="selectCategory(cat.slug)"
                    :class="[
                      'w-full text-left px-3 py-2 text-sm rounded-lg transition-colors flex items-center justify-between',
                      localFilters.category === cat.slug ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50'
                    ]"
                  >
                    <span>{{ cat.name }}</span>
                    <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">{{ cat.products_count || 0 }}</span>
                  </button>
                  <!-- Children -->
                  <div v-if="cat.children && cat.children.length && localFilters.category === cat.slug" class="ml-3 mt-1 space-y-1">
                    <button
                      v-for="child in cat.children"
                      :key="child.id"
                      @click="selectCategory(child.slug)"
                      :class="[
                        'w-full text-left px-3 py-1.5 text-xs rounded-lg transition-colors flex items-center justify-between',
                        localFilters.category === child.slug ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-50'
                      ]"
                    >
                      <span>{{ child.name }}</span>
                      <span class="text-xs text-gray-400">{{ child.products_count || 0 }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Brands -->
            <div v-if="brands && brands.length">
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Brands</label>
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label
                  v-for="brand in brands"
                  :key="brand.id"
                  class="flex items-center gap-2 cursor-pointer group"
                >
                  <input
                    type="checkbox"
                    :value="brand.slug"
                    v-model="localFilters.brands"
                    @change="applyFilters"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  />
                  <span class="text-sm text-gray-600 group-hover:text-gray-800 flex-1">{{ brand.name }}</span>
                  <span class="text-xs text-gray-400">({{ brand.products_count || 0 }})</span>
                </label>
              </div>
            </div>

            <!-- Price Range -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Price Range</label>
              <div class="flex items-center gap-2">
                <div class="flex-1">
                  <input
                    v-model.number="localFilters.min_price"
                    type="number"
                    placeholder="Min"
                    min="0"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  />
                </div>
                <span class="text-gray-400 text-sm">—</span>
                <div class="flex-1">
                  <input
                    v-model.number="localFilters.max_price"
                    type="number"
                    placeholder="Max"
                    @change="applyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  />
                </div>
              </div>
              <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>{{ currencySymbol }}{{ priceRange?.min || 0 }}</span>
                <span>{{ currencySymbol }}{{ priceRange?.max || 9999 }}</span>
              </div>
            </div>

            <!-- Rating Filter -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Minimum Rating</label>
              <div class="space-y-2">
                <button
                  v-for="stars in [4, 3, 2, 1]"
                  :key="stars"
                  @click="selectRating(stars)"
                  :class="[
                    'flex items-center gap-2 w-full px-3 py-2 rounded-lg text-sm transition-colors',
                    localFilters.rating == stars ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-gray-50 text-gray-600'
                  ]"
                >
                  <div class="flex text-yellow-400">
                    <span v-for="i in 5" :key="i" class="text-sm">{{ i <= stars ? '★' : '☆' }}</span>
                  </div>
                  <span class="text-xs">&amp; up</span>
                </button>
                <button
                  v-if="localFilters.rating"
                  @click="selectRating('')"
                  class="text-xs text-indigo-600 hover:text-indigo-800 pl-3"
                >
                  Clear rating
                </button>
              </div>
            </div>

            <!-- In Stock -->
            <div>
              <label class="flex items-center gap-3 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="localFilters.in_stock"
                  @change="applyFilters"
                  class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
                <span class="text-sm font-medium text-gray-700">In Stock Only</span>
              </label>
            </div>

            <!-- Tags -->
            <div v-if="tags && tags.length">
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Tags</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="tag in tags"
                  :key="tag.id"
                  @click="toggleTag(tag.slug)"
                  :class="[
                    'px-3 py-1 text-xs font-medium rounded-full border transition-colors',
                    localFilters.tags && localFilters.tags.includes(tag.slug)
                      ? 'bg-indigo-600 text-white border-indigo-600'
                      : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'
                  ]"
                >
                  {{ tag.name }}
                </button>
              </div>
            </div>

            <!-- Apply button (mobile) -->
            <button @click="applyFilters(); showMobileFilters = false" class="lg:hidden w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
              Apply Filters
            </button>
          </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
          <!-- Top Bar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
              <p class="text-sm text-gray-600">
                Showing <span class="font-semibold text-gray-900">{{ products?.total || 0 }}</span> products
                <span v-if="filters.search" class="ml-1">for "<span class="font-semibold">{{ filters.search }}</span>"</span>
              </p>
            </div>
            <div class="flex items-center gap-3">
              <!-- View Mode -->
              <div class="hidden sm:flex border border-gray-200 rounded-lg overflow-hidden">
                <button
                  @click="viewMode = 'grid'"
                  :class="['p-2 transition-colors', viewMode === 'grid' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-400 hover:text-gray-600']"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <button
                  @click="viewMode = 'list'"
                  :class="['p-2 transition-colors', viewMode === 'list' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-400 hover:text-gray-600']"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                </button>
              </div>

              <!-- Sort -->
              <select
                v-model="localFilters.sort"
                @change="applyFilters"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white"
              >
                <option value="">Featured</option>
                <option value="newest">Newest First</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="rating">Top Rated</option>
                <option value="popular">Most Popular</option>
                <option value="discount">Biggest Discount</option>
              </select>
            </div>
          </div>

          <!-- Active Filters -->
          <div v-if="activeFilterCount > 0" class="flex flex-wrap gap-2 mb-4">
            <span
              v-if="localFilters.search"
              class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full"
            >
              "{{ localFilters.search }}"
              <button @click="clearFilter('search')" class="hover:text-indigo-900">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </span>
            <span
              v-if="localFilters.category"
              class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full"
            >
              Category: {{ localFilters.category }}
              <button @click="clearFilter('category')" class="hover:text-indigo-900">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </span>
          </div>

          <!-- Empty State -->
          <div v-if="!products?.data?.length" class="text-center py-20">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No products found</h3>
            <p class="text-gray-500 text-sm mb-6">Try adjusting your filters or search terms</p>
            <button @click="clearAllFilters" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
              Clear Filters
            </button>
          </div>

          <!-- Product Grid -->
          <div
            v-else
            :class="[
              viewMode === 'grid'
                ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5'
                : 'space-y-4'
            ]"
          >
            <ProductCard
              v-for="product in products.data"
              :key="product.id"
              :product="product"
              :currency-symbol="currencySymbol"
            />
          </div>

          <!-- Pagination -->
          <div v-if="products?.last_page > 1" class="flex items-center justify-between mt-10">
            <p class="text-sm text-gray-500">
              Page {{ products.current_page }} of {{ products.last_page }}
            </p>
            <div class="flex items-center gap-1">
              <button
                @click="goToPage(products.current_page - 1)"
                :disabled="products.current_page <= 1"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <template v-for="pg in paginationPages" :key="pg">
                <button
                  v-if="pg !== '...'"
                  @click="goToPage(pg)"
                  :class="[
                    'px-3 py-2 text-sm border rounded-lg transition-colors min-w-[2.5rem]',
                    pg === products.current_page
                      ? 'bg-indigo-600 text-white border-indigo-600 font-semibold'
                      : 'border-gray-200 hover:bg-gray-50 text-gray-700'
                  ]"
                >
                  {{ pg }}
                </button>
                <span v-else class="px-2 text-gray-400">...</span>
              </template>
              <button
                @click="goToPage(products.current_page + 1)"
                :disabled="products.current_page >= products.last_page"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../../Layouts/ShopLayout.vue'
import ProductCard from '../../../Components/ProductCard.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  products: { type: Object, default: () => ({ data: [], total: 0, current_page: 1, last_page: 1 }) },
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
  priceRange: { type: Object, default: () => ({ min: 0, max: 9999 }) },
  filters: { type: Object, default: () => ({}) }
})

const page = usePage()
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const viewMode = ref('grid')
const showMobileFilters = ref(false)

const localFilters = reactive({
  search: props.filters?.search || '',
  category: props.filters?.category || '',
  brands: props.filters?.brands || [],
  min_price: props.filters?.min_price || '',
  max_price: props.filters?.max_price || '',
  rating: props.filters?.rating || '',
  in_stock: props.filters?.in_stock || false,
  tags: props.filters?.tags || [],
  sort: props.filters?.sort || '',
  page: 1
})

const activeFilterCount = computed(() => {
  let count = 0
  if (localFilters.search) count++
  if (localFilters.category) count++
  if (localFilters.brands?.length) count++
  if (localFilters.min_price || localFilters.max_price) count++
  if (localFilters.rating) count++
  if (localFilters.in_stock) count++
  if (localFilters.tags?.length) count++
  return count
})

const paginationPages = computed(() => {
  const current = props.products?.current_page || 1
  const last = props.products?.last_page || 1
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)
  const pages = []
  if (current <= 4) {
    pages.push(1, 2, 3, 4, 5, '...', last)
  } else if (current >= last - 3) {
    pages.push(1, '...', last - 4, last - 3, last - 2, last - 1, last)
  } else {
    pages.push(1, '...', current - 1, current, current + 1, '...', last)
  }
  return pages
})

let debounceTimer = null
function debouncedApply() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(applyFilters, 400)
}

function applyFilters() {
  const query = {}
  if (localFilters.search) query.search = localFilters.search
  if (localFilters.category) query.category = localFilters.category
  if (localFilters.brands?.length) query.brands = localFilters.brands
  if (localFilters.min_price) query.min_price = localFilters.min_price
  if (localFilters.max_price) query.max_price = localFilters.max_price
  if (localFilters.rating) query.rating = localFilters.rating
  if (localFilters.in_stock) query.in_stock = 1
  if (localFilters.tags?.length) query.tags = localFilters.tags
  if (localFilters.sort) query.sort = localFilters.sort
  query.page = 1

  router.get('/products', query, {
    preserveState: true,
    replace: true,
    preserveScroll: false
  })
}

function selectCategory(slug) {
  localFilters.category = slug
  applyFilters()
}

function selectRating(val) {
  localFilters.rating = val
  applyFilters()
}

function toggleTag(slug) {
  const idx = localFilters.tags.indexOf(slug)
  if (idx === -1) localFilters.tags.push(slug)
  else localFilters.tags.splice(idx, 1)
  applyFilters()
}

function clearFilter(key) {
  if (key === 'brands') localFilters.brands = []
  else if (key === 'tags') localFilters.tags = []
  else localFilters[key] = ''
  applyFilters()
}

function clearAllFilters() {
  localFilters.search = ''
  localFilters.category = ''
  localFilters.brands = []
  localFilters.min_price = ''
  localFilters.max_price = ''
  localFilters.rating = ''
  localFilters.in_stock = false
  localFilters.tags = []
  localFilters.sort = ''
  applyFilters()
}

function goToPage(pg) {
  const query = { ...localFilters, page: pg }
  router.get('/products', query, {
    preserveState: true,
    replace: true,
    preserveScroll: false
  })
}
</script>
