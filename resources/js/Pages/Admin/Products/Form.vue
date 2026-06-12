<template>
  <AdminLayout :title="product ? 'Edit Product' : 'Add Product'">
    <div class="flex items-center gap-3 mb-6">
      <Link href="/admin/products" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <h2 class="text-xl font-bold text-gray-800">{{ product ? 'Edit Product' : 'New Product' }}</h2>
    </div>

    <form @submit.prevent="submit">
      <div class="flex flex-col xl:flex-row gap-6">
        <!-- Left: Main content -->
        <div class="flex-1 space-y-5">

          <!-- Basic Info -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Basic Information</h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  :class="form.errors.name ? 'border-red-400' : 'border-gray-200'"
                  placeholder="e.g. Apple iPhone 15 Pro"
                />
                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                <input
                  v-model="form.short_description"
                  type="text"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  placeholder="Brief one-liner summary"
                />
                <p v-if="form.errors.short_description" class="text-xs text-red-500 mt-1">{{ form.errors.short_description }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                  v-model="form.description"
                  rows="5"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-y"
                  placeholder="Detailed product description..."
                />
                <p v-if="form.errors.description" class="text-xs text-red-500 mt-1">{{ form.errors.description }}</p>
              </div>
            </div>
          </div>

          <!-- Pricing -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Pricing</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input
                    v-model="form.price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border rounded-xl pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                    :class="form.errors.price ? 'border-red-400' : 'border-gray-200'"
                  />
                </div>
                <p v-if="form.errors.price" class="text-xs text-red-500 mt-1">{{ form.errors.price }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Compare Price</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input
                    v-model="form.compare_price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input
                    v-model="form.cost_price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Inventory -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Inventory</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input
                  v-model="form.sku"
                  type="text"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-300"
                  placeholder="e.g. PROD-001"
                />
                <p v-if="form.errors.sku" class="text-xs text-red-500 mt-1">{{ form.errors.sku }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                <input
                  v-model="form.stock"
                  type="number"
                  min="0"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                <input
                  v-model="form.low_stock_threshold"
                  type="number"
                  min="0"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                />
              </div>
            </div>
            <div class="flex flex-wrap gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.track_inventory" type="checkbox" class="rounded text-indigo-600 w-4 h-4" />
                <span class="text-sm text-gray-700">Track Inventory</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.allow_backorder" type="checkbox" class="rounded text-indigo-600 w-4 h-4" />
                <span class="text-sm text-gray-700">Allow Backorder</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.has_variants" type="checkbox" class="rounded text-indigo-600 w-4 h-4" />
                <span class="text-sm text-gray-700">Has Variants</span>
              </label>
            </div>
          </div>

          <!-- Images -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Product Images</h3>

            <!-- Existing images -->
            <div v-if="product?.images?.length" class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-4">
              <div
                v-for="img in product.images"
                :key="img.id"
                class="relative group aspect-square"
              >
                <img :src="img.url" :alt="img.alt" class="w-full h-full object-cover rounded-xl border border-gray-200" />
                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 rounded-xl transition flex flex-col items-center justify-center gap-1">
                  <button
                    type="button"
                    @click="setPrimary(img.id)"
                    class="text-xs bg-white text-gray-700 px-2 py-0.5 rounded"
                  >Primary</button>
                  <button
                    type="button"
                    @click="removeExistingImage(img.id)"
                    class="text-xs bg-red-500 text-white px-2 py-0.5 rounded"
                  >Remove</button>
                </div>
                <span v-if="img.is_primary" class="absolute top-1 left-1 bg-indigo-600 text-white text-xs px-1.5 py-0.5 rounded">Primary</span>
              </div>
            </div>

            <!-- Upload new images -->
            <label
              class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-colors"
            >
              <span class="text-3xl mb-2">📁</span>
              <span class="text-sm font-medium text-gray-600">Click or drag images here</span>
              <span class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP up to 5MB each</span>
              <input type="file" multiple accept="image/*" class="hidden" @change="handleImages" />
            </label>

            <!-- Preview new images -->
            <div v-if="imagePreviews.length" class="grid grid-cols-3 sm:grid-cols-5 gap-3 mt-4">
              <div
                v-for="(src, i) in imagePreviews"
                :key="i"
                class="relative aspect-square group"
              >
                <img :src="src" class="w-full h-full object-cover rounded-xl border border-gray-200" />
                <button
                  type="button"
                  @click="removePreview(i)"
                  class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                >✕</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="xl:w-72 space-y-5">

          <!-- Status -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Visibility</h3>
            <div class="space-y-3">
              <label class="flex items-center justify-between cursor-pointer">
                <span class="text-sm text-gray-700">Active</span>
                <div
                  @click="form.is_active = !form.is_active"
                  :class="['w-11 h-6 rounded-full relative transition-colors cursor-pointer', form.is_active ? 'bg-indigo-600' : 'bg-gray-200']"
                >
                  <span :class="['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.is_active ? 'translate-x-5' : 'translate-x-0.5']" />
                </div>
              </label>
              <label class="flex items-center justify-between cursor-pointer">
                <span class="text-sm text-gray-700">Featured</span>
                <div
                  @click="form.is_featured = !form.is_featured"
                  :class="['w-11 h-6 rounded-full relative transition-colors cursor-pointer', form.is_featured ? 'bg-indigo-600' : 'bg-gray-200']"
                >
                  <span :class="['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.is_featured ? 'translate-x-5' : 'translate-x-0.5']" />
                </div>
              </label>
              <label class="flex items-center justify-between cursor-pointer">
                <span class="text-sm text-gray-700">Mark as New</span>
                <div
                  @click="form.is_new = !form.is_new"
                  :class="['w-11 h-6 rounded-full relative transition-colors cursor-pointer', form.is_new ? 'bg-indigo-600' : 'bg-gray-200']"
                >
                  <span :class="['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.is_new ? 'translate-x-5' : 'translate-x-0.5']" />
                </div>
              </label>
            </div>
          </div>

          <!-- Category & Brand -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Organization</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select
                  v-model="form.category_id"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                >
                  <option value="">Select category</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <p v-if="form.errors.category_id" class="text-xs text-red-500 mt-1">{{ form.errors.category_id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                <select
                  v-model="form.brand_id"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                >
                  <option value="">Select brand</option>
                  <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Weight & Dimensions -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Shipping</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                <input
                  v-model="form.weight"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                />
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">L (cm)</label>
                  <input v-model="form.length" type="number" step="0.1" min="0" class="w-full border border-gray-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">W (cm)</label>
                  <input v-model="form.width" type="number" step="0.1" min="0" class="w-full border border-gray-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">H (cm)</label>
                  <input v-model="form.height" type="number" step="0.1" min="0" class="w-full border border-gray-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
              </div>
            </div>
          </div>

          <!-- Tags -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Tags</h3>
            <div class="space-y-2 max-h-40 overflow-y-auto">
              <label
                v-for="tag in tags"
                :key="tag.id"
                class="flex items-center gap-2 cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="tag.id"
                  v-model="form.tag_ids"
                  class="rounded text-indigo-600 w-4 h-4"
                />
                <span class="text-sm text-gray-700">{{ tag.name }}</span>
              </label>
              <p v-if="!tags?.length" class="text-xs text-gray-400">No tags available</p>
            </div>
          </div>

          <!-- Meta -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">SEO / Meta</h3>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input
                  v-model="form.meta_title"
                  type="text"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea
                  v-model="form.meta_description"
                  rows="3"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
                />
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl text-sm font-medium hover:opacity-90 disabled:opacity-60 transition shadow-sm"
            >
              {{ form.processing ? 'Saving...' : (product ? 'Update Product' : 'Create Product') }}
            </button>
            <Link
              href="/admin/products"
              class="w-full border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50 text-center block"
            >Cancel</Link>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  product: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
})

const form = useForm({
  name: props.product?.name ?? '',
  short_description: props.product?.short_description ?? '',
  description: props.product?.description ?? '',
  price: props.product?.price ?? '',
  compare_price: props.product?.compare_price ?? '',
  cost_price: props.product?.cost_price ?? '',
  sku: props.product?.sku ?? '',
  stock: props.product?.stock ?? 0,
  low_stock_threshold: props.product?.low_stock_threshold ?? 5,
  track_inventory: props.product?.track_inventory ?? true,
  allow_backorder: props.product?.allow_backorder ?? false,
  has_variants: props.product?.has_variants ?? false,
  is_active: props.product?.is_active ?? true,
  is_featured: props.product?.is_featured ?? false,
  is_new: props.product?.is_new ?? false,
  category_id: props.product?.category_id ?? '',
  brand_id: props.product?.brand_id ?? '',
  weight: props.product?.weight ?? '',
  length: props.product?.length ?? '',
  width: props.product?.width ?? '',
  height: props.product?.height ?? '',
  tag_ids: props.product?.tags?.map(t => t.id) ?? [],
  meta_title: props.product?.meta_title ?? '',
  meta_description: props.product?.meta_description ?? '',
  images: [],
  remove_image_ids: [],
})

const imagePreviews = ref([])

function handleImages(e) {
  const files = Array.from(e.target.files)
  form.images = [...form.images, ...files]
  files.forEach(f => {
    const reader = new FileReader()
    reader.onload = ev => imagePreviews.value.push(ev.target.result)
    reader.readAsDataURL(f)
  })
}

function removePreview(idx) {
  imagePreviews.value.splice(idx, 1)
  form.images.splice(idx, 1)
}

function setPrimary(imgId) {
  router.post(`/admin/products/${props.product.id}/images/${imgId}/primary`)
}

function removeExistingImage(imgId) {
  form.remove_image_ids.push(imgId)
}

function submit() {
  if (props.product) {
    form.post(`/admin/products/${props.product.id}`, { forceFormData: true, _method: 'PUT' })
  } else {
    form.post('/admin/products', { forceFormData: true })
  }
}
</script>
