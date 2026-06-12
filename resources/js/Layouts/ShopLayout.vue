<template>
  <div class="min-h-screen flex flex-col bg-white">
    <!-- Announcement Bar -->
    <div class="bg-indigo-600 text-white text-center py-2 text-sm font-medium tracking-wide">
      🚚 Free Shipping on orders over {{ currencySymbol }}100 &nbsp;|&nbsp; 30-Day Returns &nbsp;|&nbsp; 24/7 Support
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center space-x-8">
            <Link href="/" class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">S</span>
              </div>
              <span class="text-xl font-bold text-gray-900">{{ storeName }}</span>
            </Link>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-1">
              <Link href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                Home
              </Link>
              <Link href="/products" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                Products
              </Link>

              <!-- Categories Dropdown -->
              <div class="relative" @mouseenter="catMenuOpen = true" @mouseleave="catMenuOpen = false">
                <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition-colors flex items-center space-x-1">
                  <span>Categories</span>
                  <svg class="w-4 h-4 transition-transform" :class="catMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div v-if="catMenuOpen" class="absolute left-0 mt-0 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                  <template v-if="categories && categories.length">
                    <Link
                      v-for="cat in categories"
                      :key="cat.id"
                      :href="`/products?category=${cat.slug}`"
                      class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                    >
                      <span class="mr-2">{{ cat.icon || '📦' }}</span>
                      {{ cat.name }}
                    </Link>
                  </template>
                  <template v-else>
                    <Link href="/products" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                      All Products
                    </Link>
                  </template>
                </div>
              </div>
            </nav>
          </div>

          <!-- Right Side -->
          <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Search Bar (desktop) -->
            <form @submit.prevent="submitSearch" class="hidden md:flex items-center">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search products..."
                  class="w-48 lg:w-64 pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent transition"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </form>

            <!-- Wishlist -->
            <Link href="/wishlist" class="relative p-2 text-gray-600 hover:text-indigo-600 transition-colors">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              <span v-if="wishlistCount > 0" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-rose-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                {{ wishlistCount > 9 ? '9+' : wishlistCount }}
              </span>
            </Link>

            <!-- Cart -->
            <Link href="/cart" class="relative p-2 text-gray-600 hover:text-indigo-600 transition-colors">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span v-if="cartCount > 0" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                {{ cartCount > 9 ? '9+' : cartCount }}
              </span>
            </Link>

            <!-- User Menu -->
            <div class="relative hidden sm:block" @click.stop>
              <button
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center space-x-1 p-2 rounded-full text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
              >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </button>

              <div v-if="userMenuOpen" class="absolute right-0 mt-1 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                <template v-if="auth?.user">
                  <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth.user.name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth.user.email }}</p>
                  </div>
                  <Link href="/profile" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    My Profile
                  </Link>
                  <Link href="/orders" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    My Orders
                  </Link>
                  <Link href="/wishlist" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Wishlist
                  </Link>
                  <div class="border-t border-gray-100 mt-1">
                    <button @click="logout" class="flex items-center w-full px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                      <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                      Sign Out
                    </button>
                  </div>
                </template>
                <template v-else>
                  <Link href="/login" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Sign In
                  </Link>
                  <Link href="/register" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create Account
                  </Link>
                </template>
              </div>
            </div>

            <!-- Mobile Hamburger -->
            <button
              @click="mobileOpen = !mobileOpen"
              class="lg:hidden p-2 rounded-md text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
            >
              <svg v-if="!mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileOpen" class="lg:hidden bg-white border-t border-gray-100 shadow-lg">
        <div class="px-4 py-3">
          <!-- Mobile Search -->
          <form @submit.prevent="submitSearch" class="mb-3">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search products..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300"
              />
              <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </form>

          <nav class="space-y-1">
            <Link href="/" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Home</Link>
            <Link href="/products" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Products</Link>

            <div class="pl-3">
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide py-1">Categories</p>
              <template v-if="categories && categories.length">
                <Link
                  v-for="cat in categories"
                  :key="cat.id"
                  :href="`/products?category=${cat.slug}`"
                  class="block px-3 py-1.5 text-sm text-gray-700 hover:text-indigo-600"
                >
                  {{ cat.icon || '📦' }} {{ cat.name }}
                </Link>
              </template>
            </div>

            <div class="border-t border-gray-100 pt-2 mt-2">
              <template v-if="auth?.user">
                <Link href="/profile" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">My Profile</Link>
                <Link href="/orders" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">My Orders</Link>
                <button @click="logout" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-rose-600 hover:bg-rose-50">Sign Out</button>
              </template>
              <template v-else>
                <Link href="/login" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Sign In</Link>
                <Link href="/register" class="block px-3 py-2 rounded-md text-sm font-medium text-indigo-600 hover:bg-indigo-50">Create Account</Link>
              </template>
            </div>
          </nav>
        </div>
      </div>
    </header>

    <!-- Flash Notifications -->
    <transition
      enter-active-class="transform transition duration-300"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transform transition duration-200"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <div
        v-if="showFlash"
        class="fixed bottom-6 right-6 z-[100] max-w-sm w-full"
      >
        <div
          v-if="flash?.success"
          class="flex items-start p-4 bg-white border-l-4 border-green-500 rounded-lg shadow-xl"
        >
          <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800">Success</p>
            <p class="text-sm text-gray-600 mt-0.5">{{ flash.success }}</p>
          </div>
          <button @click="showFlash = false" class="ml-2 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div
          v-else-if="flash?.error"
          class="flex items-start p-4 bg-white border-l-4 border-rose-500 rounded-lg shadow-xl"
        >
          <div class="flex-shrink-0 w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center mr-3">
            <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800">Error</p>
            <p class="text-sm text-gray-600 mt-0.5">{{ flash.error }}</p>
          </div>
          <button @click="showFlash = false" class="ml-2 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
    </transition>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
      <!-- Main Footer -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- About -->
          <div>
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold">S</span>
              </div>
              <span class="text-xl font-bold text-white">{{ storeName }}</span>
            </div>
            <p class="text-sm text-gray-400 leading-relaxed mb-4">
              Your one-stop destination for quality products at unbeatable prices. Shop with confidence.
            </p>
            <div class="flex space-x-3">
              <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
              </a>
              <a href="#" class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Quick Links</h3>
            <ul class="space-y-2">
              <li><Link href="/" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Home</Link></li>
              <li><Link href="/products" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">All Products</Link></li>
              <li><Link href="/cart" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Shopping Cart</Link></li>
              <li><Link href="/orders" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">My Orders</Link></li>
              <li><Link href="/wishlist" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Wishlist</Link></li>
              <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">About Us</a></li>
              <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Contact</a></li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h3 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Contact Us</h3>
            <ul class="space-y-3">
              <li class="flex items-start space-x-2">
                <svg class="w-4 h-4 text-indigo-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-sm text-gray-400">123 Commerce St, San Francisco, CA 94102</span>
              </li>
              <li class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <a href="mailto:support@shopvue.com" class="text-sm text-gray-400 hover:text-indigo-400">support@shopvue.com</a>
              </li>
              <li class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <a href="tel:+11234567890" class="text-sm text-gray-400 hover:text-indigo-400">+1 (123) 456-7890</a>
              </li>
              <li class="text-sm text-gray-400">Mon–Fri: 9am–6pm PST</li>
            </ul>
          </div>

          <!-- Newsletter -->
          <div>
            <h3 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Newsletter</h3>
            <p class="text-sm text-gray-400 mb-4">Subscribe for exclusive deals and updates.</p>
            <form @submit.prevent="subscribeNewsletter" class="space-y-2">
              <input
                v-model="newsletterEmail"
                type="email"
                placeholder="Enter your email"
                required
                class="w-full px-4 py-2.5 text-sm bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
              <button
                type="submit"
                class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-500 transition-colors"
              >
                Subscribe
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
          <p class="text-xs text-gray-500">&copy; {{ new Date().getFullYear() }} {{ storeName }}. All rights reserved.</p>
          <div class="flex items-center space-x-4">
            <a href="#" class="text-xs text-gray-500 hover:text-gray-400">Privacy Policy</a>
            <a href="#" class="text-xs text-gray-500 hover:text-gray-400">Terms of Service</a>
            <a href="#" class="text-xs text-gray-500 hover:text-gray-400">Cookie Policy</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const auth = computed(() => page.props.auth)
const cartCount = computed(() => page.props.cartCount ?? 0)
const wishlistCount = computed(() => page.props.wishlistCount ?? 0)
const flash = computed(() => page.props.flash ?? {})
const storeName = computed(() => page.props.storeName || 'ShopVue')
const currencySymbol = computed(() => page.props.currencySymbol || '$')
const categories = computed(() => page.props.navCategories || [])

const mobileOpen = ref(false)
const userMenuOpen = ref(false)
const catMenuOpen = ref(false)
const showFlash = ref(false)
const searchQuery = ref('')
const newsletterEmail = ref('')

watch(flash, (val) => {
  if (val?.success || val?.error) {
    showFlash.value = true
    setTimeout(() => { showFlash.value = false }, 3000)
  }
}, { immediate: true, deep: true })

// Close menus on outside click
if (typeof window !== 'undefined') {
  document.addEventListener('click', () => {
    userMenuOpen.value = false
  })
}

function logout() {
  router.post('/logout')
}

function submitSearch() {
  if (searchQuery.value.trim()) {
    router.get('/products', { search: searchQuery.value.trim() }, { preserveState: false })
  }
}

function subscribeNewsletter() {
  router.post('/newsletter/subscribe', { email: newsletterEmail.value }, {
    preserveScroll: true,
    onSuccess: () => { newsletterEmail.value = '' }
  })
}
</script>
