<template>
  <AdminLayout :title="brand ? 'Edit Brand' : 'Add Brand'">
    <div class="flex items-center gap-3 mb-6">
      <Link href="/admin/brands" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <h2 class="text-xl font-bold text-gray-800">{{ brand ? 'Edit Brand' : 'New Brand' }}</h2>
    </div>

    <div class="max-w-2xl">
      <form @submit.prevent="submit" class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Brand Details</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name <span class="text-red-500">*</span></label>
            <input
              v-model="form.name"
              type="text"
              class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              :class="form.errors.name ? 'border-red-400' : 'border-gray-200'"
              placeholder="e.g. Apple"
            />
            <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
              placeholder="Short description about this brand"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
            <input
              v-model="form.website"
              type="url"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              placeholder="https://brand.com"
            />
            <p v-if="form.errors.website" class="text-xs text-red-500 mt-1">{{ form.errors.website }}</p>
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

        <!-- Logo Upload -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Brand Logo</h3>
          <div v-if="logoPreview || brand?.logo" class="mb-4 flex items-center gap-4">
            <div class="w-24 h-24 bg-gray-50 border border-gray-200 rounded-2xl overflow-hidden flex items-center justify-center">
              <img
                :src="logoPreview || brand.logo"
                :alt="brand?.name"
                class="w-20 h-20 object-contain"
              />
            </div>
            <button type="button" @click="removeLogo" class="text-red-500 text-sm hover:text-red-700">Remove</button>
          </div>
          <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl py-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-colors">
            <span class="text-3xl mb-2">🏷️</span>
            <span class="text-sm font-medium text-gray-600">Upload logo</span>
            <span class="text-xs text-gray-400 mt-1">PNG, SVG, JPG up to 2MB</span>
            <input type="file" accept="image/*" class="hidden" @change="handleLogo" />
          </label>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl text-sm font-medium hover:opacity-90 disabled:opacity-60 transition"
          >
            {{ form.processing ? 'Saving...' : (brand ? 'Update Brand' : 'Create Brand') }}
          </button>
          <Link
            href="/admin/brands"
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
  brand: { type: Object, default: null },
})

const form = useForm({
  name: props.brand?.name ?? '',
  description: props.brand?.description ?? '',
  website: props.brand?.website ?? '',
  is_active: props.brand?.is_active ?? true,
  logo: null,
})

const logoPreview = ref(null)

function handleLogo(e) {
  const file = e.target.files[0]
  if (!file) return
  form.logo = file
  const reader = new FileReader()
  reader.onload = ev => logoPreview.value = ev.target.result
  reader.readAsDataURL(file)
}

function removeLogo() {
  form.logo = null
  logoPreview.value = null
}

function submit() {
  if (props.brand) {
    form.post(`/admin/brands/${props.brand.id}`, { forceFormData: true, _method: 'PUT' })
  } else {
    form.post('/admin/brands', { forceFormData: true })
  }
}
</script>
