<template>
  <AdminLayout :title="coupon ? 'Edit Coupon' : 'Add Coupon'">
    <div class="flex items-center gap-3 mb-6">
      <Link href="/admin/coupons" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </Link>
      <h2 class="text-xl font-bold text-gray-800">{{ coupon ? 'Edit Coupon' : 'New Coupon' }}</h2>
    </div>

    <div class="max-w-2xl">
      <form @submit.prevent="submit" class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
          <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Coupon Details</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
              <input
                v-model="form.code"
                type="text"
                class="w-full border rounded-xl px-4 py-2.5 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-300"
                :class="form.errors.code ? 'border-red-400' : 'border-gray-200'"
                placeholder="e.g. SAVE20"
              />
              <p v-if="form.errors.code" class="text-xs text-red-500 mt-1">{{ form.errors.code }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                :class="form.errors.name ? 'border-red-400' : 'border-gray-200'"
                placeholder="Coupon name"
              />
              <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="2"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-300"
              placeholder="Optional internal notes"
            />
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.type" type="radio" value="percentage" class="text-indigo-600 w-4 h-4" />
                <span class="text-sm text-gray-700">Percentage (%)</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.type" type="radio" value="fixed" class="text-indigo-600 w-4 h-4" />
                <span class="text-sm text-gray-700">Fixed Amount ($)</span>
              </label>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Value <span class="text-red-500">*</span>
                <span class="text-gray-400">({{ form.type === 'percentage' ? '%' : '$' }})</span>
              </label>
              <input
                v-model="form.value"
                type="number"
                step="0.01"
                min="0"
                class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                :class="form.errors.value ? 'border-red-400' : 'border-gray-200'"
              />
              <p v-if="form.errors.value" class="text-xs text-red-500 mt-1">{{ form.errors.value }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order ($)</label>
              <input
                v-model="form.minimum_order"
                type="number"
                step="0.01"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div v-if="form.type === 'percentage'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Max Discount ($)</label>
              <input
                v-model="form.maximum_discount"
                type="number"
                step="0.01"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="No limit"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
              <input
                v-model="form.usage_limit"
                type="number"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="Unlimited"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Per User Limit</label>
              <input
                v-model="form.per_user_limit"
                type="number"
                min="0"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="Unlimited"
              />
            </div>
          </div>

          <!-- Dates -->
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
              <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
              <input
                v-model="form.expires_at"
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

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl text-sm font-medium hover:opacity-90 disabled:opacity-60 transition"
          >
            {{ form.processing ? 'Saving...' : (coupon ? 'Update Coupon' : 'Create Coupon') }}
          </button>
          <Link
            href="/admin/coupons"
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

defineOptions({ layout: undefined })

const props = defineProps({
  coupon: { type: Object, default: null },
})

const form = useForm({
  code: props.coupon?.code ?? '',
  name: props.coupon?.name ?? '',
  description: props.coupon?.description ?? '',
  type: props.coupon?.type ?? 'percentage',
  value: props.coupon?.value ?? '',
  minimum_order: props.coupon?.minimum_order ?? '',
  maximum_discount: props.coupon?.maximum_discount ?? '',
  usage_limit: props.coupon?.usage_limit ?? '',
  per_user_limit: props.coupon?.per_user_limit ?? '',
  is_active: props.coupon?.is_active ?? true,
  starts_at: props.coupon?.starts_at ?? '',
  expires_at: props.coupon?.expires_at ?? '',
})

function submit() {
  if (props.coupon) {
    form.put(`/admin/coupons/${props.coupon.id}`)
  } else {
    form.post('/admin/coupons')
  }
}
</script>
