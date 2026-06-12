<template>
  <div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-extrabold text-gray-900">My Wishlist</h1>
          <p class="text-gray-500 mt-1">{{ items?.length || 0 }} saved item{{ items?.length !== 1 ? 's' : '' }}</p>
        </div>
        <div class="flex items-center gap-3">
          <button
            v-if="items && items.length"
            @click="addAllToCart"
            :disabled="addingAll"
            class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-60 transition-colors flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Add All to Cart
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!items || !items.length" class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Your wishlist is empty</h2>
        <p class="text-gray-500 mb-6 max-w-sm">Save items you love and come back to them later. It's easy to add products to your wishlist!</p>
        <Link href="/products" class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors">
          Start Shopping
        </Link>
      </div>

      <!-- Wishlist Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        <div
          v-for="item in items"
          :key="item.id"
          class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col"
        >
          <!-- Image -->
          <div class="relative overflow-hidden bg-gray-50 aspect-square">
            <Link :href="`/products/${item.product_slug}`">
              <img
                :src="item.primaryImage || '/images/placeholder.png'"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                @error="$event.target.src = '/images/placeholder.png'"
              />
            </Link>

            <!-- Badges -->
            <div class="absolute top-2 left-2 flex flex-col gap-1">
              <span v-if="item.is_new" class="px-2 py-0.5 bg-indigo-600 text-white text-xs font-bold rounded-md">NEW</span>
              <span v-if="getDiscountPercent(item) > 0" class="px-2 py-0.5 bg-rose-500 text-white text-xs font-bold rounded-md">-{{ getDiscountPercent(item) }}%</span>
            </div>

            <!-- Remove from Wishlist -->
            <button
              @click="removeFromWishlist(item)"
              class="absolute top-2 right-2 w-8 h-8 bg-white text-rose-500 rounded-full flex items-center justify-center shadow-sm hover:bg-rose-500 hover:text-white transition-all"
              title="Remove from Wishlist"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>

            <!-- Date added -->
            <div class="absolute bottom-2 left-2 bg-white/80 backdrop-blur text-xs text-gray-500 px-2 py-0.5 rounded-full">
              Saved {{ formatDate(item.added_at) }}
            </div>
          </div>

          <!-- Info -->
          <div class="flex flex-col flex-1 p-4">
            <p v-if="item.category" class="text-xs text-indigo-500 font-medium uppercase tracking-wide mb-1 truncate">
              {{ item.category?.name || item.category }}
            </p>
            <Link :href="`/products/${item.product_slug}`" class="block mb-2">
              <h3 class="text-sm font-semibold text-gray-800 hover:text-indigo-600 transition-colors line-clamp-2 leading-snug">
                {{ item.name }}
              </h3>
            </Link>

            <StarRating
              v-if="item.avg_rating"
              :rating="item.avg_rating"
              :count="item.review_count"
              size="sm"
              :show-count="true"
              class="mb-2"
            />

            <!-- Price -->
            <div class="flex items-baseline gap-2 mb-3 mt-auto">
              <span class="text-lg font-bold text-gray-900">{{ currencySymbol }}{{ formatPrice(item.price) }}</span>
              <span v-if="item.compare_price && item.compare_price > item.price" class="text-sm text-gray-400 line-through">
                {{ currencySymbol }}{{ formatPrice(item.compare_price) }}
              </span>
            </div>

            <!-- Stock Status -->
            <div v-if="item.stock <= 0" class="mb-3">
              <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">Out of Stock</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button
                @click="addToCart(item)"
                :disabled="addingId === item.id || item.stock <= 0"
                class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-1"
              >
                <svg v-if="addingId !== item.id" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ item.stock <= 0 ? 'Unavailable' : 'Add to Cart' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Recommendations -->
      <div v-if="recommended && recommended.length" class="mt-16">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">You Might Also Like</h2>
          <Link href="/products" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            Browse All <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          <ProductCard
            v-for="product in recommended"
            :key="product.id"
            :product="product"
            :currency-symbol="currencySymbol"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../Layouts/ShopLayout.vue'
import ProductCard from '../../Components/ProductCard.vue'
import StarRating from '../../Components/StarRating.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  items: { type: Array, default: () => [] },
  recommended: { type: Array, default: () => [] }
})

const page = usePage()
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const addingId = ref(null)
const addingAll = ref(false)

function formatPrice(price) {
  return Number(price || 0).toFixed(2)
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

function getDiscountPercent(item) {
  if (item.compare_price && item.compare_price > item.price) {
    return Math.round((1 - item.price / item.compare_price) * 100)
  }
  return 0
}

function removeFromWishlist(item) {
  router.delete(`/wishlist/${item.product_id || item.id}`, { preserveScroll: true })
}

function addToCart(item) {
  addingId.value = item.id
  router.post('/cart/add', {
    product_id: item.product_id || item.id,
    quantity: 1
  }, {
    preserveScroll: true,
    onFinish: () => { addingId.value = null }
  })
}

function addAllToCart() {
  addingAll.value = true
  router.post('/wishlist/add-all-to-cart', {}, {
    preserveScroll: true,
    onFinish: () => { addingAll.value = false }
  })
}
</script>
