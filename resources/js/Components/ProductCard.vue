<template>
  <div class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
    <!-- Image Container -->
    <div class="relative overflow-hidden bg-gray-50 aspect-square">
      <Link :href="`/products/${product.slug}`">
        <img
          v-if="product.primaryImage"
          :src="product.primaryImage"
          :alt="product.name"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          loading="lazy"
          @error="imageError = true"
        />
        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
          <svg class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
      </Link>

      <!-- Badges -->
      <div class="absolute top-2 left-2 flex flex-col gap-1">
        <span v-if="product.is_new" class="inline-flex items-center px-2 py-0.5 bg-indigo-600 text-white text-xs font-bold rounded-md">
          NEW
        </span>
        <span v-if="discountPercent > 0" class="inline-flex items-center px-2 py-0.5 bg-rose-500 text-white text-xs font-bold rounded-md">
          -{{ discountPercent }}%
        </span>
      </div>

      <!-- Wishlist Button -->
      <button
        @click.prevent="toggleWishlist"
        :class="[
          'absolute top-2 right-2 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 shadow-sm',
          isWishlisted
            ? 'bg-rose-500 text-white'
            : 'bg-white text-gray-400 hover:bg-rose-50 hover:text-rose-500'
        ]"
        :title="isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'"
      >
        <svg class="w-4 h-4" :fill="isWishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
      </button>

      <!-- Quick View overlay -->
      <div class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
        <Link
          :href="`/products/${product.slug}`"
          class="block w-full py-2.5 bg-indigo-600 text-white text-sm font-semibold text-center hover:bg-indigo-700 transition-colors"
        >
          Quick View
        </Link>
      </div>
    </div>

    <!-- Product Info -->
    <div class="flex flex-col flex-1 p-4">
      <!-- Category -->
      <p v-if="product.category" class="text-xs text-indigo-500 font-medium uppercase tracking-wide mb-1 truncate">
        {{ product.category?.name || product.category }}
      </p>

      <!-- Name -->
      <Link :href="`/products/${product.slug}`" class="block mb-2">
        <h3 class="text-sm font-semibold text-gray-800 hover:text-indigo-600 transition-colors line-clamp-2 leading-snug">
          {{ product.name }}
        </h3>
      </Link>

      <!-- Rating -->
      <div class="mb-2">
        <StarRating
          :rating="product.avg_rating || 0"
          :count="product.review_count || 0"
          size="sm"
          :show-count="true"
        />
      </div>

      <!-- Price -->
      <div class="flex items-baseline gap-2 mb-3 mt-auto">
        <span class="text-lg font-bold text-gray-900">
          {{ currencySymbol }}{{ formatPrice(product.price) }}
        </span>
        <span v-if="product.compare_price && product.compare_price > product.price" class="text-sm text-gray-400 line-through">
          {{ currencySymbol }}{{ formatPrice(product.compare_price) }}
        </span>
      </div>

      <!-- Add to Cart Button -->
      <button
        @click="addToCart"
        :disabled="adding"
        class="w-full py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2"
      >
        <svg v-if="!adding" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        {{ adding ? 'Adding...' : 'Add to Cart' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import StarRating from './StarRating.vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  currencySymbol: {
    type: String,
    default: '$'
  }
})

const adding = ref(false)
const imageError = ref(false)
const isWishlisted = ref(props.product.is_wishlisted || false)

const discountPercent = computed(() => {
  if (props.product.compare_price && props.product.compare_price > props.product.price) {
    return Math.round((1 - props.product.price / props.product.compare_price) * 100)
  }
  return 0
})

function formatPrice(price) {
  return Number(price).toFixed(2)
}

function addToCart() {
  adding.value = true
  router.post('/cart/add', {
    product_id: props.product.id,
    quantity: 1
  }, {
    preserveScroll: true,
    onFinish: () => { adding.value = false }
  })
}

function toggleWishlist() {
  if (isWishlisted.value) {
    router.delete(`/wishlist/${props.product.id}`, { preserveScroll: true })
    isWishlisted.value = false
  } else {
    router.post('/wishlist', { product_id: props.product.id }, { preserveScroll: true })
    isWishlisted.value = true
  }
}
</script>
