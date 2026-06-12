<template>
  <AdminLayout :title="banner ? 'Edit Banner' : 'Add Banner'">
    <div class="flex items-center gap-3 mb-6">
      <Link href="/admin/banners" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <h2 class="text-xl font-bold text-gray-800">{{ banner ? 'Edit Banner' : 'New Banner' }}</h2>
    </div>

    <div class="max-w-2xl">
      <form @submit.prevent="submit" class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Banner Details</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input
              v-model="form.title"
              type="text"
              class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              :class="form.errors.title ? 'border-red-400' : 'border-gray-200'"
              placeholder="Banner title"
            />
            <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
            <input
              v-model="form.subtitle"
              type="text"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              placeholder="Optional subtitle text"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
              <input
                v-model="form.button_text"
                type="text"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="e.g. Shop Now"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
              <input
                v-model="form.button_link"
                type="text"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="/shop"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
              <select
                v-model="form.position"
                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              >
                <option value="home">Home</option>
                <option value="sidebar">Sidebar</option>
                <option value="category">Category</option>
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
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
              <input
                v-model="form.starts_at"
                type="datetime-local"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ends At</label>
              <input
                v-model="form.ends_at"
                type="datetime-local"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              />
            </div>
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
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Banner Image</h3>
          <div v-if="imagePreview || banner?.image" class="mb-4">
            <img
              :src="imagePreview || banner.image"
              alt="Preview"
              class="w-full h-40 object-cover rounded-xl border border-gray-200"
            />
          </div>
          <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-colors">
            <span class="text-3xl mb-2">🖼️</span>
            <span class="text-sm font-medium text-gray-600">Upload banner image</span>
            <span class="text-xs text-gray-400 mt-1">Recommended: 1200×400px, PNG or JPG</span>
            <input type="file" accept="image/*" class="hidden" @change="handleImage" />
          </label>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl text-sm font-medium hover:opacity-90 disabled:opacity-60 transition"
          >
            {{ form.processing ? 'Saving...' : (banner ? 'Update Banner' : 'Create Banner') }}
          </button>
          <Link
            href="/admin/banners"
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
import { ref } from 'vue'

defineOptions({ layout: undefined })

const props = defineProps({
  banner: { type: Object, default: null },
})

const form = useForm({
  title: props.banner?.title ?? '',
  subtitle: props.banner?.subtitle ?? '',
  button_text: props.banner?.button_text ?? '',
  button_link: props.banner?.button_link ?? '',
  position: props.banner?.position ?? 'home',
  sort_order: props.banner?.sort_order ?? 0,
  is_active: props.banner?.is_active ?? true,
  starts_at: props.banner?.starts_at ?? '',
  ends_at: props.banner?.ends_at ?? '',
  image: null,
})

const imagePreview = ref(null)

function handleImage(e) {
  const file = e.target.files[0]
  if (!file) return
  form.image = file
  const reader = new FileReader()
  reader.onload = ev => imagePreview.value = ev.target.result
  reader.readAsDataURL(file)
}

function submit() {
  if (props.banner) {
    form.post(`/admin/banners/${props.banner.id}`, { forceFormData: true, _method: 'PUT' })
  } else {
    form.post('/admin/banners', { forceFormData: true })
  }
}
</script>
