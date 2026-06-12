<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  product: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.product)
const title = computed(() => isEdit.value ? 'Edit Product' : 'Create Product')

const form = useForm({
  name: props.product?.name ?? '',
  short_description: props.product?.short_description ?? '',
  description: props.product?.description ?? '',
  sku: props.product?.sku ?? '',
  price: props.product?.price ?? '',
  compare_price: props.product?.compare_price ?? '',
  cost_price: props.product?.cost_price ?? '',
  stock: props.product?.stock ?? 0,
  low_stock_threshold: props.product?.low_stock_threshold ?? 5,
  category_id: props.product?.category_id ?? '',
  brand_id: props.product?.brand_id ?? '',
  weight: props.product?.weight ?? '',
  dimensions: props.product?.dimensions ?? '',
  is_active: props.product?.is_active ?? true,
  is_featured: props.product?.is_featured ?? false,
  is_new: props.product?.is_new ?? true,
  track_inventory: props.product?.track_inventory ?? true,
  allow_backorder: props.product?.allow_backorder ?? false,
  has_variants: props.product?.has_variants ?? false,
  meta_title: props.product?.meta_title ?? '',
  meta_description: props.product?.meta_description ?? '',
  tags: props.product?.tags?.map(t => t.id) ?? [],
  images: [],
})

const imagePreview = ref([])

function handleImages(e) {
  form.images = Array.from(e.target.files)
  imagePreview.value = []
  form.images.forEach(file => {
    const reader = new FileReader()
    reader.onload = (ev) => imagePreview.value.push(ev.target.result)
    reader.readAsDataURL(file)
  })
}

function toggleTag(tagId) {
  const idx = form.tags.indexOf(tagId)
  if (idx >= 0) form.tags.splice(idx, 1)
  else form.tags.push(tagId)
}

function submit() {
  if (isEdit.value) {
    form.post(route('admin.products.update', props.product.id), { _method: 'PUT' })
  } else {
    form.post(route('admin.products.store'))
  }
}

function deleteImage(imageId) {
  router.delete(route('admin.products.images.delete', imageId), { preserveScroll: true })
}

function setPrimary(imageId) {
  router.patch(route('admin.products.images.primary', imageId), {}, { preserveScroll: true })
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex items-center gap-4 mb-8">
      <Link
        :href="route('admin.products.index')"
        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Products</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
      </div>
    </div>

    <form @submit.prevent="submit">
      <div class="grid grid-cols-12 gap-6">

        <!-- ===== LEFT COLUMN (col-span-8) ===== -->
        <div class="col-span-12 lg:col-span-8 space-y-6">

          <!-- Basic Information -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">
              Basic Information
            </h2>
            <div class="space-y-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                  Product Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="e.g. Apple iPhone 15 Pro"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description</label>
                <textarea
                  v-model="form.short_description"
                  rows="3"
                  placeholder="Brief summary shown in product listings..."
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition resize-none"
                  :class="form.errors.short_description ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.short_description" class="mt-1.5 text-xs text-red-600">{{ form.errors.short_description }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Description</label>
                <textarea
                  v-model="form.description"
                  rows="8"
                  placeholder="Detailed product description..."
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition resize-y"
                  :class="form.errors.description ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.description" class="mt-1.5 text-xs text-red-600">{{ form.errors.description }}</p>
              </div>
            </div>
          </div>

          <!-- Pricing -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Pricing</h2>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                  Price ($) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input
                    v-model="form.price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="w-full rounded-xl border pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                    :class="form.errors.price ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                  />
                </div>
                <p v-if="form.errors.price" class="mt-1.5 text-xs text-red-600">{{ form.errors.price }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Compare Price ($)</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input
                    v-model="form.compare_price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="w-full rounded-xl border border-gray-200 pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                    :class="form.errors.compare_price ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                  />
                </div>
                <p v-if="form.errors.compare_price" class="mt-1.5 text-xs text-red-600">{{ form.errors.compare_price }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cost Price ($)</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input
                    v-model="form.cost_price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="w-full rounded-xl border border-gray-200 pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                    :class="form.errors.cost_price ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                  />
                </div>
                <p v-if="form.errors.cost_price" class="mt-1.5 text-xs text-red-600">{{ form.errors.cost_price }}</p>
              </div>
            </div>
          </div>

          <!-- Inventory -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Inventory</h2>
            <div class="grid grid-cols-3 gap-4 mb-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">SKU</label>
                <input
                  v-model="form.sku"
                  type="text"
                  placeholder="e.g. PROD-001"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.sku ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.sku" class="mt-1.5 text-xs text-red-600">{{ form.errors.sku }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Stock Quantity</label>
                <input
                  v-model="form.stock"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.stock ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.stock" class="mt-1.5 text-xs text-red-600">{{ form.errors.stock }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Low Stock Threshold</label>
                <input
                  v-model="form.low_stock_threshold"
                  type="number"
                  min="0"
                  placeholder="5"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.low_stock_threshold ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.low_stock_threshold" class="mt-1.5 text-xs text-red-600">{{ form.errors.low_stock_threshold }}</p>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-5 border-t border-gray-100">
              <label class="flex items-start gap-3 cursor-pointer">
                <input
                  v-model="form.track_inventory"
                  type="checkbox"
                  class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <div>
                  <span class="block text-sm font-medium text-gray-700">Track Inventory</span>
                  <span class="block text-xs text-gray-400 mt-0.5">Monitor stock levels</span>
                </div>
              </label>

              <label class="flex items-start gap-3 cursor-pointer">
                <input
                  v-model="form.allow_backorder"
                  type="checkbox"
                  class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <div>
                  <span class="block text-sm font-medium text-gray-700">Allow Backorder</span>
                  <span class="block text-xs text-gray-400 mt-0.5">Sell when out of stock</span>
                </div>
              </label>

              <label class="flex items-start gap-3 cursor-pointer">
                <input
                  v-model="form.has_variants"
                  type="checkbox"
                  class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <div>
                  <span class="block text-sm font-medium text-gray-700">Has Variants</span>
                  <span class="block text-xs text-gray-400 mt-0.5">Multiple options/sizes</span>
                </div>
              </label>
            </div>
          </div>

          <!-- SEO -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">SEO</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Title</label>
                <input
                  v-model="form.meta_title"
                  type="text"
                  placeholder="SEO title (defaults to product name if empty)"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.meta_title ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.meta_title" class="mt-1.5 text-xs text-red-600">{{ form.errors.meta_title }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Description</label>
                <textarea
                  v-model="form.meta_description"
                  rows="3"
                  placeholder="SEO description (150-160 characters recommended)"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition resize-none"
                  :class="form.errors.meta_description ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.meta_description" class="mt-1.5 text-xs text-red-600">{{ form.errors.meta_description }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== RIGHT COLUMN (col-span-4) ===== -->
        <div class="col-span-12 lg:col-span-4 space-y-6">

          <!-- Status -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Status</h2>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-700">Active</p>
                  <p class="text-xs text-gray-400">{{ form.is_active ? 'Visible in store' : 'Hidden from store' }}</p>
                </div>
                <button
                  type="button"
                  @click="form.is_active = !form.is_active"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  :class="form.is_active ? 'bg-green-500' : 'bg-gray-200'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                    :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-700">Featured</p>
                  <p class="text-xs text-gray-400">Show in featured section</p>
                </div>
                <button
                  type="button"
                  @click="form.is_featured = !form.is_featured"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  :class="form.is_featured ? 'bg-indigo-500' : 'bg-gray-200'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                    :class="form.is_featured ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-700">New Arrival</p>
                  <p class="text-xs text-gray-400">Display "New" badge</p>
                </div>
                <button
                  type="button"
                  @click="form.is_new = !form.is_new"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  :class="form.is_new ? 'bg-amber-500' : 'bg-gray-200'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                    :class="form.is_new ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </div>
          </div>

          <!-- Organization -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Organization</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                <select
                  v-model="form.category_id"
                  class="w-full rounded-xl border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-white"
                  :class="form.errors.category_id ? 'border-red-400' : 'border-gray-200'"
                >
                  <option value="">Select a category</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <p v-if="form.errors.category_id" class="mt-1.5 text-xs text-red-600">{{ form.errors.category_id }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand</label>
                <select
                  v-model="form.brand_id"
                  class="w-full rounded-xl border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-white"
                  :class="form.errors.brand_id ? 'border-red-400' : 'border-gray-200'"
                >
                  <option value="">Select a brand</option>
                  <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                </select>
                <p v-if="form.errors.brand_id" class="mt-1.5 text-xs text-red-600">{{ form.errors.brand_id }}</p>
              </div>
            </div>
          </div>

          <!-- Dimensions -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Dimensions</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Weight (kg)</label>
                <input
                  v-model="form.weight"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="e.g. 0.5"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.weight ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.weight" class="mt-1.5 text-xs text-red-600">{{ form.errors.weight }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dimensions (L×W×H)</label>
                <input
                  v-model="form.dimensions"
                  type="text"
                  placeholder="e.g. 30×20×10 cm"
                  class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                  :class="form.errors.dimensions ? 'border-red-400 bg-red-50' : 'border-gray-200'"
                />
                <p v-if="form.errors.dimensions" class="mt-1.5 text-xs text-red-600">{{ form.errors.dimensions }}</p>
              </div>
            </div>
          </div>

          <!-- Tags -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Tags</h2>
            <div v-if="tags && tags.length" class="grid grid-cols-2 gap-1.5 max-h-48 overflow-y-auto pr-1">
              <label
                v-for="tag in tags"
                :key="tag.id"
                class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-indigo-50 transition"
              >
                <input
                  type="checkbox"
                  :value="tag.id"
                  :checked="form.tags.includes(tag.id)"
                  @change="toggleTag(tag.id)"
                  class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <span class="text-sm text-gray-700 truncate">{{ tag.name }}</span>
              </label>
            </div>
            <p v-else class="text-sm text-gray-400 italic">No tags available.</p>
            <p v-if="form.errors.tags" class="mt-2 text-xs text-red-600">{{ form.errors.tags }}</p>
          </div>

          <!-- Product Images -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Product Images</h2>

            <label class="block cursor-pointer">
              <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-indigo-400 hover:bg-indigo-50 transition">
                <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm font-medium text-gray-600">Click to upload images</p>
                <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP up to 5MB each</p>
              </div>
              <input type="file" multiple accept="image/*" @change="handleImages" class="sr-only" />
            </label>
            <p v-if="form.errors.images" class="mt-1.5 text-xs text-red-600">{{ form.errors.images }}</p>

            <!-- New image previews -->
            <div v-if="imagePreview.length" class="mt-4 grid grid-cols-3 gap-2">
              <div
                v-for="(src, idx) in imagePreview"
                :key="idx"
                class="relative aspect-square rounded-xl overflow-hidden bg-gray-100"
              >
                <img :src="src" class="w-full h-full object-cover" alt="Preview" />
                <div class="absolute top-1 left-1">
                  <span class="bg-indigo-600 text-white text-xs px-1.5 py-0.5 rounded font-medium">New</span>
                </div>
              </div>
            </div>

            <!-- Existing images -->
            <div v-if="product?.images?.length" class="mt-5">
              <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">Existing Images</p>
              <div class="space-y-2">
                <div
                  v-for="image in product.images"
                  :key="image.id"
                  class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-100 hover:border-gray-200 transition"
                >
                  <img
                    :src="image.url"
                    class="h-12 w-12 rounded-lg object-cover flex-shrink-0 border border-gray-100"
                    alt="Product image"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 truncate">{{ image.filename ?? 'image' }}</p>
                    <span v-if="image.is_primary" class="inline-block text-xs text-green-600 font-semibold">
                      Primary
                    </span>
                  </div>
                  <div class="flex gap-1 flex-shrink-0">
                    <button
                      v-if="!image.is_primary"
                      type="button"
                      @click="setPrimary(image.id)"
                      class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-2 py-1 rounded-lg hover:bg-indigo-50 transition"
                    >
                      Set Primary
                    </button>
                    <button
                      type="button"
                      @click="deleteImage(image.id)"
                      class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 rounded-lg hover:bg-red-50 transition"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="mt-8 flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
        <Link
          :href="route('admin.products.index')"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition"
        >
          Cancel
        </Link>
        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-60 disabled:cursor-not-allowed shadow-sm"
        >
          <svg
            v-if="form.processing"
            class="animate-spin h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          {{ form.processing ? 'Saving...' : (isEdit ? 'Update Product' : 'Create Product') }}
        </button>
      </div>
    </form>
  </div>
</template>
