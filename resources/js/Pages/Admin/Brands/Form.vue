<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  brand: { type: Object, default: null },
})

const isEdit = computed(() => !!props.brand)

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
  reader.onload = ev => { logoPreview.value = ev.target.result }
  reader.readAsDataURL(file)
}

function removeLogo() {
  form.logo = null
  logoPreview.value = null
}

function submit() {
  if (isEdit.value) {
    form.post(route('admin.brands.update', props.brand.id), { forceFormData: true, _method: 'PUT' })
  } else {
    form.post(route('admin.brands.store'), { forceFormData: true })
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="flex items-center gap-4 mb-8">
      <Link
        :href="route('admin.brands.index')"
        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Brands</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Edit Brand' : 'Create Brand' }}</h1>
      </div>
    </div>

    <form @submit.prevent="submit" class="space-y-6">

      <!-- Brand Details -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Brand Details</h2>

        <div class="space-y-5">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
              Brand Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              placeholder="e.g. Apple"
              class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
              :class="form.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200'"
            />
            <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Short description about this brand..."
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition resize-none"
              :class="form.errors.description ? 'border-red-400 bg-red-50' : 'border-gray-200'"
            />
            <p v-if="form.errors.description" class="mt-1.5 text-xs text-red-600">{{ form.errors.description }}</p>
          </div>

          <!-- Website URL -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Website URL</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
              </svg>
              <input
                v-model="form.website"
                type="url"
                placeholder="https://brand.com"
                class="w-full rounded-xl border pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
                :class="form.errors.website ? 'border-red-400 bg-red-50' : 'border-gray-200'"
              />
            </div>
            <p v-if="form.errors.website" class="mt-1.5 text-xs text-red-600">{{ form.errors.website }}</p>
          </div>

          <!-- Is Active Toggle -->
          <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-xl border border-gray-200">
            <div>
              <p class="text-sm font-medium text-gray-700">Active</p>
              <p class="text-xs text-gray-400">{{ form.is_active ? 'Brand is visible in the store' : 'Brand is hidden from the store' }}</p>
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

      <!-- Logo Upload -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Brand Logo</h2>

        <!-- Preview -->
        <div v-if="logoPreview || brand?.logo" class="mb-5 flex items-center gap-5">
          <div class="w-24 h-24 bg-gray-50 border border-gray-200 rounded-2xl overflow-hidden flex items-center justify-center">
            <img
              :src="logoPreview || brand.logo"
              :alt="brand?.name ?? 'Logo preview'"
              class="w-20 h-20 object-contain"
            />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-700 mb-1">Current Logo</p>
            <button
              type="button"
              @click="removeLogo"
              class="text-sm text-red-500 hover:text-red-700 font-medium transition"
            >
              Remove logo
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
              {{ logoPreview || brand?.logo ? 'Click to replace logo' : 'Click to upload a logo' }}
            </p>
            <p class="text-xs text-gray-400 mt-1">PNG, SVG, JPG up to 2MB</p>
          </div>
          <input type="file" accept="image/*" @change="handleLogo" class="sr-only" />
        </label>
        <p v-if="form.errors.logo" class="mt-1.5 text-xs text-red-600">{{ form.errors.logo }}</p>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-4 pt-2">
        <Link
          :href="route('admin.brands.index')"
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
          {{ form.processing ? 'Saving...' : (isEdit ? 'Update Brand' : 'Create Brand') }}
        </button>
      </div>
    </form>
  </div>
</template>
