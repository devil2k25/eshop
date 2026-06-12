<template>
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Account</h1>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-8">
      <nav class="flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            'pb-4 text-sm font-medium border-b-2 transition-colors',
            activeTab === tab.key
              ? 'border-indigo-600 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Profile Tab -->
    <div v-if="activeTab === 'profile'" class="space-y-8">
      <!-- Profile Info -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h2>
        <form @submit.prevent="submitProfile" class="space-y-5">
          <div class="flex items-center space-x-5 mb-6">
            <div class="relative">
              <div v-if="profileForm.avatar_preview || user.avatar" class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-indigo-500">
                <img :src="profileForm.avatar_preview || `/storage/${user.avatar}`" class="w-full h-full object-cover" alt="Avatar" />
              </div>
              <div v-else class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                {{ user.name?.charAt(0).toUpperCase() }}
              </div>
              <label class="absolute -bottom-1 -right-1 w-7 h-7 bg-white rounded-full shadow border border-gray-200 flex items-center justify-center cursor-pointer hover:bg-gray-50">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input type="file" class="sr-only" accept="image/*" @change="handleAvatarChange" />
              </label>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700">{{ user.name }}</p>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
              <input v-model="profileForm.name" type="text" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
              <p v-if="profileForm.errors.name" class="mt-1 text-xs text-red-600">{{ profileForm.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
              <input v-model="profileForm.email" type="email" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
              <p v-if="profileForm.errors.email" class="mt-1 text-xs text-red-600">{{ profileForm.errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
              <input v-model="profileForm.phone" type="tel" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
              <p v-if="profileForm.errors.phone" class="mt-1 text-xs text-red-600">{{ profileForm.errors.phone }}</p>
            </div>
          </div>

          <div class="flex justify-end">
            <button type="submit" :disabled="profileForm.processing" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
              {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>
        <form @submit.prevent="submitPassword" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input v-model="passwordForm.current_password" type="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-600">{{ passwordForm.errors.current_password }}</p>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
              <input v-model="passwordForm.password" type="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
              <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-red-600">{{ passwordForm.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
              <input v-model="passwordForm.password_confirmation" type="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>
          </div>
          <div class="flex justify-end">
            <button type="submit" :disabled="passwordForm.processing" class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 disabled:opacity-50 transition-colors">
              {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Orders Tab -->
    <div v-if="activeTab === 'orders'">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
        </div>
        <div v-if="orders.length === 0" class="text-center py-16">
          <svg class="mx-auto w-12 h-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          <p class="text-gray-500">No orders yet</p>
          <Link href="/products" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">Start Shopping →</Link>
        </div>
        <table v-else class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ order.order_number }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ order.created_at }}</td>
              <td class="px-6 py-4">
                <span :class="statusClass(order.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ Number(order.total).toFixed(2) }}</td>
              <td class="px-6 py-4 text-right">
                <Link :href="`/orders/${order.id}`" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
          <Link href="/orders" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View all orders →</Link>
        </div>
      </div>
    </div>

    <!-- Addresses Tab -->
    <div v-if="activeTab === 'addresses'" class="space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Saved Addresses</h2>
        <button @click="showAddressForm = true; editingAddress = null; resetAddressForm()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
          + Add Address
        </button>
      </div>

      <!-- Address Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div
          v-for="addr in addresses"
          :key="addr.id"
          class="bg-white rounded-xl border border-gray-200 p-5 relative"
          :class="addr.is_default ? 'border-indigo-400 ring-1 ring-indigo-400' : ''"
        >
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-2">
              <span class="font-semibold text-gray-900 text-sm">{{ addr.name }}</span>
              <span v-if="addr.is_default" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Default</span>
            </div>
            <div class="flex items-center space-x-1">
              <button @click="editAddress(addr)" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </button>
              <button @click="deleteAddr(addr.id)" class="p-1.5 text-gray-400 hover:text-red-600 rounded">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
          <p class="text-sm text-gray-600">{{ addr.phone }}</p>
          <p class="text-sm text-gray-600 mt-1">{{ addr.address_line1 }}{{ addr.address_line2 ? ', ' + addr.address_line2 : '' }}</p>
          <p class="text-sm text-gray-600">{{ addr.city }}, {{ addr.state }} {{ addr.zip_code }}</p>
          <p class="text-sm text-gray-600">{{ addr.country }}</p>
          <button
            v-if="!addr.is_default"
            @click="setDefault(addr.id)"
            class="mt-3 text-xs text-indigo-600 hover:text-indigo-700 font-medium"
          >
            Set as default
          </button>
        </div>

        <!-- Empty state -->
        <div v-if="addresses.length === 0" class="col-span-2 text-center py-12 bg-white rounded-xl border border-gray-200">
          <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <p class="text-sm text-gray-500">No saved addresses</p>
        </div>
      </div>

      <!-- Address Form Modal -->
      <div v-if="showAddressForm" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showAddressForm = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-5">{{ editingAddress ? 'Edit Address' : 'Add New Address' }}</h3>
          <form @submit.prevent="submitAddress" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2 sm:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input v-model="addressForm.name" type="text" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.name" class="mt-1 text-xs text-red-600">{{ addressForm.errors.name }}</p>
              </div>
              <div class="col-span-2 sm:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input v-model="addressForm.phone" type="tel" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.phone" class="mt-1 text-xs text-red-600">{{ addressForm.errors.phone }}</p>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                <input v-model="addressForm.address_line1" type="text" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.address_line1" class="mt-1 text-xs text-red-600">{{ addressForm.errors.address_line1 }}</p>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 <span class="text-gray-400">(optional)</span></label>
                <input v-model="addressForm.address_line2" type="text" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input v-model="addressForm.city" type="text" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.city" class="mt-1 text-xs text-red-600">{{ addressForm.errors.city }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                <input v-model="addressForm.state" type="text" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.state" class="mt-1 text-xs text-red-600">{{ addressForm.errors.state }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                <input v-model="addressForm.zip_code" type="text" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.zip_code" class="mt-1 text-xs text-red-600">{{ addressForm.errors.zip_code }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <input v-model="addressForm.country" type="text" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                <p v-if="addressForm.errors.country" class="mt-1 text-xs text-red-600">{{ addressForm.errors.country }}</p>
              </div>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-2">
              <button type="button" @click="showAddressForm = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
              <button type="submit" :disabled="addressForm.processing" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                {{ addressForm.processing ? 'Saving...' : (editingAddress ? 'Update' : 'Save Address') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  user: Object,
  addresses: Array,
  orders: Array,
})

const activeTab = ref('profile')
const tabs = [
  { key: 'profile', label: 'Profile' },
  { key: 'orders', label: 'Orders' },
  { key: 'addresses', label: 'Addresses' },
]

// Profile form
const profileForm = useForm({
  name: props.user?.name ?? '',
  email: props.user?.email ?? '',
  phone: props.user?.phone ?? '',
  avatar: null,
  avatar_preview: null,
})

function handleAvatarChange(e) {
  const file = e.target.files[0]
  if (!file) return
  profileForm.avatar = file
  const reader = new FileReader()
  reader.onload = (ev) => { profileForm.avatar_preview = ev.target.result }
  reader.readAsDataURL(file)
}

function submitProfile() {
  profileForm.post('/profile', { preserveScroll: true })
}

// Password form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

function submitPassword() {
  passwordForm.patch('/profile/password', {
    preserveScroll: true,
    onSuccess: () => passwordForm.reset(),
  })
}

// Address form
const showAddressForm = ref(false)
const editingAddress = ref(null)

const addressForm = useForm({
  name: '',
  phone: '',
  address_line1: '',
  address_line2: '',
  city: '',
  state: '',
  zip_code: '',
  country: 'US',
})

function resetAddressForm() {
  addressForm.reset()
  Object.assign(addressForm, { name: '', phone: '', address_line1: '', address_line2: '', city: '', state: '', zip_code: '', country: 'US' })
}

function editAddress(addr) {
  editingAddress.value = addr
  Object.assign(addressForm, {
    name: addr.name,
    phone: addr.phone,
    address_line1: addr.address_line1,
    address_line2: addr.address_line2 ?? '',
    city: addr.city,
    state: addr.state,
    zip_code: addr.zip_code,
    country: addr.country,
  })
  showAddressForm.value = true
}

function submitAddress() {
  if (editingAddress.value) {
    addressForm.patch(`/profile/addresses/${editingAddress.value.id}`, {
      preserveScroll: true,
      onSuccess: () => { showAddressForm.value = false; editingAddress.value = null },
    })
  } else {
    addressForm.post('/profile/addresses', {
      preserveScroll: true,
      onSuccess: () => { showAddressForm.value = false },
    })
  }
}

function deleteAddr(id) {
  if (confirm('Delete this address?')) {
    router.delete(`/profile/addresses/${id}`, { preserveScroll: true })
  }
}

function setDefault(id) {
  router.patch(`/profile/addresses/${id}/default`, {}, { preserveScroll: true })
}

function statusClass(status) {
  const map = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    refunded: 'bg-gray-100 text-gray-800',
  }
  return map[status] ?? 'bg-gray-100 text-gray-800'
}
</script>
