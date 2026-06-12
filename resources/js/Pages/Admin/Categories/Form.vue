<template>
  <AdminLayout :title="category ? 'Edit Category' : 'Add Category'">
    <div class="flex items-center gap-3 mb-6">
      <Link href="/admin/categories" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <h2 class="text-xl font-bold text-gray-800">{{ category ? 'Edit Category' : 'New Category' }}</h2>
    </div>

    <div class="max-w-2xl">
      <form @submit.prevent="submit" class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Category Details</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input
              v-model="form.name"
              type="text"
              class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              :class="form.errors.name ? 'border-red-400' : 'border-gray-200'"
              placeholder="Category name"
            />
            <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
              placeholder="Optional description"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
            <select
              v-model="form.parent_id"
              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
            >
              <option value="">None (Top Level)</option>
              <option
                v-for="cat in availableParents"
                :key="cat.id"
                :value="cat.id"
              >{{ cat.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
            <input
              v-model="form.sort_order"
              type="number"
              min="0"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
            />
          </div>

          <label class="flex items-center gap-3 cursor-pointer">
            <div
              @click="form.is_active = !form.is_active"
              :class="['w-11 h-6 rounded-full relative transition-colors cursor-pointer flex-shrink-0', form.is_active ? 'bg-indigo-600' : 'bg-gray-200']"
            >
              <span :class="['absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform', form.is_active ? 'translate-x-5' : 'translate-x-0.5']" />
            </div>
            <span class="text-sm text-gray-700">Active</span>
          </label>
        </div>

        <!-- Image Upload -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Category Image</h3>
          <div v-if="imagePreview || category?.image" class="mb-4">
            <img
              :src="imagePreview || category.image"
              alt="Preview"
              class="w-32 h-32 object-cover rounded-xl border border-gray-200"
            />
          </div>
          <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-colors">
            <span class="text-3xl mb-2">🖼️</span>
            <span class="text-sm font-medium text-gray-600">Upload image</span>
            <span class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</span>
            <input type="file" accept="image/*" class="hidden" @change="handleImage" />
          </label>
        </div>

        <!-- Meta -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">SEO / Meta</h3>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
            <input
              v-model="form.meta_title"
              type="text"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
            <textarea
              v-model="form.meta_description"
              rows="3"
              class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl text-sm font-medium hover:opacity-90 disabled:opacity-60 transition"
          >
            {{ form.processing ? 'Saving...' : (category ? 'Update Category' : 'Create Category') }}
          </button>
          <Link
            href="/admin/categories"
            class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50 text-center"
          >Cancel</Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  category: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
})

const form = useForm({
  name: props.category?.name ?? '',
  description: props.category?.description ?? '',
  parent_id: props.category?.parent_id ?? '',
  sort_order: props.category?.sort_order ?? 0,
  is_active: props.category?.is_active ?? true,
  meta_title: props.category?.meta_title ?? '',
  meta_description: props.category?.meta_description ?? '',
  image: null,
})

const imagePreview = ref(null)

const availableParents = computed(() =>
  props.categories.filter(c => c.id !== props.category?.id)
)

function handleImage(e) {
  const file = e.target.files[0]
  if (!file) return
  form.image = file
  const reader = new FileReader()
  reader.onload = ev => imagePreview.value = ev.target.result
  reader.readAsDataURL(file)
}

function submit() {
  if (props.category) {
    form.post(`/admin/categories/${props.category.id}`, { forceFormData: true, _method: 'PUT' })
  } else {
    form.post('/admin/categories', { forceFormData: true })
  }
}
</script>
