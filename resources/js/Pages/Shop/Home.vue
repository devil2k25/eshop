<template>
  <div>
    <!-- Hero Banner Carousel -->
    <section class="relative overflow-hidden bg-gray-900" style="min-height: 500px;">
      <div class="relative h-full" style="min-height: 500px;">
        <transition-group name="banner-fade" tag="div">
          <div
            v-for="(banner, index) in (banners && banners.length ? banners : defaultBanners)"
            :key="banner.id || index"
            v-show="currentBanner === index"
            class="absolute inset-0"
          >
            <!-- Background Image -->
            <img
              v-if="banner.image"
              :src="banner.image"
              :alt="banner.title"
              class="w-full h-full object-cover"
            />
            <div
              v-else
              class="w-full h-full"
              :style="{ background: banner.gradient || 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)' }"
            ></div>
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>

            <!-- Content -->
            <div class="absolute inset-0 flex items-center">
              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="max-w-xl">
                  <span v-if="banner.badge" class="inline-block px-3 py-1 bg-indigo-500 text-white text-xs font-bold rounded-full uppercase tracking-wide mb-4">
                    {{ banner.badge }}
                  </span>
                  <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                    {{ banner.title }}
                  </h1>
                  <p class="text-lg text-gray-200 mb-8 leading-relaxed">
                    {{ banner.subtitle }}
                  </p>
                  <div class="flex flex-wrap gap-3">
                    <Link
                      :href="banner.cta_link || '/products'"
                      class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500 transition-colors shadow-lg"
                    >
                      {{ banner.cta_text || 'Shop Now' }}
                      <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </Link>
                    <Link
                      href="/products"
                      class="inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur text-white font-semibold rounded-xl hover:bg-white/20 transition-colors border border-white/20"
                    >
                      Explore All
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </transition-group>
      </div>

      <!-- Navigation Arrows -->
      <button
        @click="prevBanner"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors z-10"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
      <button
        @click="nextBanner"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors z-10"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- Dots -->
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
        <button
          v-for="(_, index) in (banners && banners.length ? banners : defaultBanners)"
          :key="index"
          @click="currentBanner = index"
          :class="[
            'w-2.5 h-2.5 rounded-full transition-all duration-300',
            currentBanner === index ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/80'
          ]"
        ></button>
      </div>
    </section>

    <!-- Feature Strip -->
    <section class="bg-indigo-600 text-white py-3">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center divide-x divide-indigo-500 gap-y-2">
          <div class="flex items-center px-6 gap-2">
            <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium">Free Shipping over $100</span>
          </div>
          <div class="flex items-center px-6 gap-2">
            <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span class="text-sm font-medium">30-Day Returns</span>
          </div>
          <div class="flex items-center px-6 gap-2">
            <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="text-sm font-medium">24/7 Support</span>
          </div>
          <div class="flex items-center px-6 gap-2">
            <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span class="text-sm font-medium">Secure Checkout</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Shop by Category -->
    <section v-if="categories && categories.length" class="py-14 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Shop by Category</h2>
            <p class="text-gray-500 mt-1">Find exactly what you're looking for</p>
          </div>
          <Link href="/products" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            View All
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <Link
            v-for="cat in categories"
            :key="cat.id"
            :href="`/products?category=${cat.slug}`"
            class="group flex flex-col items-center p-4 bg-white rounded-2xl border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all duration-200"
          >
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center mb-3 transition-colors">
              <img v-if="cat.image" :src="cat.image" :alt="cat.name" class="w-9 h-9 object-contain" />
              <span v-else class="text-2xl">{{ cat.icon || '📦' }}</span>
            </div>
            <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-600 text-center leading-tight transition-colors">{{ cat.name }}</span>
            <span v-if="cat.products_count" class="text-xs text-gray-400 mt-0.5">{{ cat.products_count }} items</span>
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section v-if="featuredProducts && featuredProducts.length" class="py-14 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
            <p class="text-gray-500 mt-1">Handpicked selections just for you</p>
          </div>
          <Link href="/products?sort=featured" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            View All <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
            :currency-symbol="currencySymbol"
          />
        </div>
      </div>
    </section>

    <!-- Mid Banner Strip -->
    <section class="py-10 bg-gradient-to-r from-indigo-600 to-purple-600">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="text-white text-center md:text-left">
            <h3 class="text-2xl font-bold mb-2">Special Offer — Up to 40% Off!</h3>
            <p class="text-indigo-200">Limited time deals on thousands of items. Shop before they're gone.</p>
          </div>
          <Link
            href="/products?sort=discount"
            class="flex-shrink-0 px-8 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-colors shadow-lg"
          >
            Shop the Sale
          </Link>
        </div>
      </div>
    </section>

    <!-- New Arrivals -->
    <section v-if="newArrivals && newArrivals.length" class="py-14 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">New Arrivals</h2>
            <p class="text-gray-500 mt-1">Fresh picks added this week</p>
          </div>
          <Link href="/products?sort=newest" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            View All <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in newArrivals"
            :key="product.id"
            :product="product"
            :currency-symbol="currencySymbol"
          />
        </div>
      </div>
    </section>

    <!-- Best Sellers -->
    <section v-if="bestSellers && bestSellers.length" class="py-14 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Best Sellers</h2>
            <p class="text-gray-500 mt-1">Customer favorites you'll love</p>
          </div>
          <Link href="/products?sort=popular" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            View All <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in bestSellers"
            :key="product.id"
            :product="product"
            :currency-symbol="currencySymbol"
          />
        </div>
      </div>
    </section>

    <!-- Trust Badges -->
    <section class="py-12 bg-gray-50 border-t border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h4 class="font-bold text-gray-800">100% Secure</h4>
            <p class="text-sm text-gray-500 mt-1">SSL encrypted checkout</p>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h4 class="font-bold text-gray-800">Quality Guarantee</h4>
            <p class="text-sm text-gray-500 mt-1">All products verified</p>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h4 class="font-bold text-gray-800">Fast Delivery</h4>
            <p class="text-sm text-gray-500 mt-1">Ships within 24 hours</p>
          </div>
          <div class="flex flex-col items-center">
            <div class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <h4 class="font-bold text-gray-800">Easy Returns</h4>
            <p class="text-sm text-gray-500 mt-1">30-day hassle-free</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../Layouts/ShopLayout.vue'
import ProductCard from '../../Components/ProductCard.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  banners: { type: Array, default: () => [] },
  featuredProducts: { type: Array, default: () => [] },
  newArrivals: { type: Array, default: () => [] },
  bestSellers: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] }
})

const page = usePage()
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const defaultBanners = [
  {
    id: 1,
    title: 'Discover Amazing Products',
    subtitle: 'Shop the latest trends with unbeatable prices and free shipping on orders over $100.',
    cta_text: 'Shop Now',
    cta_link: '/products',
    badge: 'New Season',
    gradient: 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)'
  },
  {
    id: 2,
    title: 'Summer Sale — Up to 50% Off',
    subtitle: 'Limited time offers on thousands of hand-picked products. Grab yours before it\'s gone.',
    cta_text: 'View Deals',
    cta_link: '/products?sort=discount',
    badge: 'Sale',
    gradient: 'linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%)'
  },
  {
    id: 3,
    title: 'New Arrivals Every Week',
    subtitle: 'Be the first to own the latest products. Fresh stock added weekly just for you.',
    cta_text: 'Shop New',
    cta_link: '/products?sort=newest',
    badge: 'Fresh Picks',
    gradient: 'linear-gradient(135deg, #10b981 0%, #0891b2 100%)'
  }
]

const currentBanner = ref(0)
let autoPlayInterval = null

function nextBanner() {
  const list = props.banners?.length ? props.banners : defaultBanners
  currentBanner.value = (currentBanner.value + 1) % list.length
}

function prevBanner() {
  const list = props.banners?.length ? props.banners : defaultBanners
  currentBanner.value = (currentBanner.value - 1 + list.length) % list.length
}

onMounted(() => {
  autoPlayInterval = setInterval(nextBanner, 5000)
})

onUnmounted(() => {
  if (autoPlayInterval) clearInterval(autoPlayInterval)
})
</script>

<style scoped>
.banner-fade-enter-active,
.banner-fade-leave-active {
  transition: opacity 0.6s ease;
}
.banner-fade-enter-from,
.banner-fade-leave-to {
  opacity: 0;
}

section[style] {
  min-height: 500px;
}
</style>
