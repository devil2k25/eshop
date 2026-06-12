<template>
  <div class="flex h-screen bg-gray-100 overflow-hidden">
    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black bg-opacity-60 lg:hidden"
      @click="sidebarOpen = false"
    />

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 transform transition-transform duration-300 ease-in-out flex flex-col',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
        <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
          <span class="text-white font-bold text-lg">S</span>
        </div>
        <div>
          <span class="text-white font-bold text-lg leading-none">ShopVue</span>
          <span class="block text-indigo-400 text-xs font-medium tracking-widest uppercase">Admin</span>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
        <template v-for="item in navItems" :key="item.href">
          <Link
            :href="item.href"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group',
              isActive(item.href)
                ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-900/40'
                : 'text-slate-400 hover:bg-slate-800 hover:text-white'
            ]"
          >
            <span class="text-base w-5 text-center">{{ item.icon }}</span>
            <span>{{ item.name }}</span>
            <span
              v-if="item.badge"
              class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 leading-none"
            >{{ item.badge }}</span>
          </Link>
        </template>
      </nav>

      <!-- User / Logout at bottom -->
      <div class="border-t border-slate-700 px-4 py-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
            {{ userInitial }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white text-sm font-medium truncate">{{ page.props.auth?.user?.name ?? 'Admin' }}</p>
            <p class="text-slate-400 text-xs truncate">{{ page.props.auth?.user?.email ?? '' }}</p>
          </div>
          <button
            @click="logout"
            class="text-slate-400 hover:text-red-400 transition-colors p-1 rounded"
            title="Logout"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
      <!-- Top Header -->
      <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 sm:px-6 h-16">
          <div class="flex items-center gap-4">
            <!-- Hamburger -->
            <button
              class="lg:hidden text-gray-500 hover:text-gray-700 p-1 rounded-lg"
              @click="sidebarOpen = !sidebarOpen"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <!-- Page Title -->
            <div>
              <h1 class="text-lg font-semibold text-gray-800">{{ title }}</h1>
              <slot name="breadcrumb">
                <p class="text-xs text-gray-400">Admin Panel</p>
              </slot>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <!-- Slot for extra header actions -->
            <slot name="header-actions" />
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                {{ userInitial }}
              </div>
              <span class="hidden sm:block text-sm font-medium text-gray-700">{{ page.props.auth?.user?.name ?? 'Admin' }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Flash Messages -->
      <div v-if="flash.success || flash.error" class="px-4 sm:px-6 pt-4">
        <div
          v-if="flash.success"
          class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm"
        >
          <span class="text-lg">✅</span>
          <span>{{ flash.success }}</span>
          <button @click="dismissFlash('success')" class="ml-auto text-emerald-600 hover:text-emerald-800">✕</button>
        </div>
        <div
          v-if="flash.error"
          class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm mt-2"
        >
          <span class="text-lg">❌</span>
          <span>{{ flash.error }}</span>
          <button @click="dismissFlash('error')" class="ml-auto text-red-600 hover:text-red-800">✕</button>
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const props = defineProps({ title: String })
const sidebarOpen = ref(false)
const page = usePage()

const navItems = [
  { name: 'Dashboard', href: '/admin', icon: '📊' },
  { name: 'Products', href: '/admin/products', icon: '📦' },
  { name: 'Categories', href: '/admin/categories', icon: '🗂️' },
  { name: 'Brands', href: '/admin/brands', icon: '🏷️' },
  { name: 'Orders', href: '/admin/orders', icon: '🛒' },
  { name: 'Customers', href: '/admin/customers', icon: '👥' },
  { name: 'Coupons', href: '/admin/coupons', icon: '🎟️' },
  { name: 'Banners', href: '/admin/banners', icon: '🖼️' },
  { name: 'Reviews', href: '/admin/reviews', icon: '⭐' },
  { name: 'Reports', href: '/admin/reports', icon: '📈' },
  { name: 'Settings', href: '/admin/settings', icon: '⚙️' },
]

const flash = ref({
  success: page.props.flash?.success ?? null,
  error: page.props.flash?.error ?? null,
})

function dismissFlash(type) {
  flash.value[type] = null
}

const userInitial = computed(() => {
  const name = page.props.auth?.user?.name ?? 'A'
  return name.charAt(0).toUpperCase()
})

function isActive(href) {
  if (href === '/admin') return page.url === '/admin' || page.url === '/admin/'
  return page.url.startsWith(href)
}

function logout() {
  router.post('/logout')
}
</script>
