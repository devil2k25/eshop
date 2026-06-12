<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  category: { type: Object, default: null },
  parentCategories: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.category)

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
  props.parentCategories.filter(c => c.id !== props.category?.id)
)

function handleImage(e) {
  const file = e.target.files[0]
  if (!file) return
  form.image = file
  const reader = new FileReader()
  reader.onload = ev => { imagePreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function removeImage() {
  form.image = null
  imagePreview.value = null
}

function submit() {
  if (isEdit.value) {
    form.post(route('admin.categories.update', props.category.id), { forceFormData: true, _method: 'PUT' })
  } else {
    form.post(route('admin.categories.store'), { forceFormData: true })
  }
}
</script>

<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="flex items-center gap-4 mb-8">
      <Link
        :href="route('admin.categories.index')"
        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Categories</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Edit Category' : 'Create Category' }}</h1>
      </div>
    </div>

    <form @submit.prevent="submit" class="space-y-6">

      <!-- Category Details -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Category Details</h2>

        <div class="space-y-5">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
              Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Category name"
              class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
              :class="form.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200'"
            />
            <p class="mt-1.5 text-xs text-gray-400">The slug will be automatically generated from the name.</p>
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Optional description for this category..."
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition resize-none"
              :class="form.errors.description ? 'border-red-400 bg-red-50' : 'border-gray-200'"
            />
            <p v-if="form.errors.description" class="mt-1.5 text-xs text-red-600">{{ form.errors.description }}</p>
          </div>

          <!-- Parent Category -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent Category</label>
            <select
              v-model="form.parent_id"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-white"
              :class="form.errors.parent_id ? 'border-red-400' : 'border-gray-200'"
            >
              <option value="">No parent (Top-level category)</option>
              <option
                v-for="parent in availableParents"
                :key="parent.id"
                :value="parent.id"
              >
                {{ parent.name }}
              </option>
            </select>
            <p v-if="form.errors.parent_id" class="mt-1.5 text-xs text-red-600">{{ form.errors.parent_id }}</p>
          </div>

          <!-- Sort Order -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
              <input
                v-model="form.sort_order"
                type="number"
                min="0"
                placeholder="0"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                :class="form.errors.sort_order ? 'border-red-400 bg-red-50' : 'border-gray-200'"
              />
              <p class="mt-1.5 text-xs text-gray-400">Lower numbers appear first.</p>
              <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">{{ form.errors.sort_order }}</p>
            </div>

            <!-- Is Active Toggle -->
            <div class="flex items-end pb-1">
              <div class="flex items-center justify-between w-full px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200">
                <div>
                  <p class="text-sm font-medium text-gray-700">Active</p>
                  <p class="text-xs text-gray-400">{{ form.is_active ? 'Visible in store' : 'Hidden' }}</p>
                </div>
                <button
                  type="button"
                  @click="form.is_active = !form.is_active"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  :class="form.is_active ? 'bg-green-500' : 'bg-gray-300'"
                >
                  <span
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                    :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"
                  />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Image Upload -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Category Image</h2>

        <!-- Preview -->
        <div v-if="imagePreview || category?.image" class="mb-5 flex items-start gap-4">
          <img
            :src="imagePreview || category.image"
            alt="Category image preview"
            class="w-32 h-32 object-cover rounded-xl border border-gray-200"
          />
          <div class="flex flex-col gap-2 justify-center h-32">
            <p class="text-sm font-medium text-gray-700">Current Image</p>
            <button
              type="button"
              @click="removeImage"
              class="text-sm text-red-500 hover:text-red-700 font-medium transition"
            >
              Remove image
            </button>
          </div>
        </div>

        <!-- Upload Zone -->
        <label class="block cursor-pointer">
          <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition">
            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm font-medium text-gray-600">
              {{ imagePreview || category?.image ? 'Click to replace image' : 'Click to upload an image' }}
            </p>
            <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
          </div>
          <input type="file" accept="image/*" @change="handleImage" class="sr-only" />
        </label>
        <p v-if="form.errors.image" class="mt-1.5 text-xs text-red-600">{{ form.errors.image }}</p>
      </div>

      <!-- SEO / Meta -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">SEO / Meta</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Title</label>
            <input
              v-model="form.meta_title"
              type="text"
              placeholder="SEO title (defaults to category name)"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
              :class="form.errors.meta_title ? 'border-red-400 bg-red-50' : 'border-gray-200'"
            />
            <p v-if="form.errors.meta_title" class="mt-1.5 text-xs text-red-600">{{ form.errors.meta_title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Description</label>
            <textarea
              v-model="form.meta_description"
              rows="3"
              placeholder="SEO meta description (150-160 characters recommended)"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition resize-none"
              :class="form.errors.meta_description ? 'border-red-400 bg-red-50' : 'border-gray-200'"
            />
            <p v-if="form.errors.meta_description" class="mt-1.5 text-xs text-red-600">{{ form.errors.meta_description }}</p>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-4 pt-2">
        <Link
          :href="route('admin.categories.index')"
          class="inline-flex items-center px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition"
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
          {{ form.processing ? 'Saving...' : (isEdit ? 'Update Category' : 'Create Category') }}
        </button>
      </div>
    </form>
  </div>
</template>
