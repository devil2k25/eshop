<template>
  <div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center flex-wrap gap-1 text-sm text-gray-500 mb-8">
        <Link href="/" class="hover:text-indigo-600 transition-colors">Home</Link>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <Link v-if="product.category" :href="`/products?category=${product.category.slug}`" class="hover:text-indigo-600 transition-colors">
          {{ product.category.name }}
        </Link>
        <template v-if="product.category">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </template>
        <span class="text-gray-800 font-medium truncate max-w-xs">{{ product.name }}</span>
      </nav>

      <!-- Product Main Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
        <!-- Left: Image Gallery -->
        <div class="space-y-4">
          <!-- Main Image -->
          <div class="relative aspect-square overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 group">
            <img
              :src="activeImage || product.primaryImage || '/images/placeholder.png'"
              :alt="product.name"
              class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
              @error="$event.target.src = '/images/placeholder.png'"
            />
            <!-- Zoom hint -->
            <div class="absolute top-3 right-3 bg-white/80 backdrop-blur rounded-full px-2 py-1 text-xs text-gray-500 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              Hover to zoom
            </div>
            <!-- Badges -->
            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
              <span v-if="product.is_new" class="px-2.5 py-0.5 bg-indigo-600 text-white text-xs font-bold rounded-md">NEW</span>
              <span v-if="discountPercent > 0" class="px-2.5 py-0.5 bg-rose-500 text-white text-xs font-bold rounded-md">-{{ discountPercent }}%</span>
            </div>
          </div>

          <!-- Thumbnails -->
          <div v-if="product.images && product.images.length > 1" class="flex gap-2 overflow-x-auto pb-1">
            <button
              v-for="(img, i) in product.images"
              :key="i"
              @click="activeImage = img.url || img"
              :class="[
                'flex-shrink-0 w-16 h-16 rounded-xl border-2 overflow-hidden transition-all',
                activeImage === (img.url || img) ? 'border-indigo-500 shadow-md' : 'border-gray-100 hover:border-gray-300'
              ]"
            >
              <img :src="img.url || img" :alt="`${product.name} - ${i + 1}`" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Right: Product Details -->
        <div class="flex flex-col">
          <!-- Category + Brand -->
          <div class="flex items-center gap-2 mb-2">
            <Link v-if="product.category" :href="`/products?category=${product.category.slug}`" class="text-xs font-semibold text-indigo-500 uppercase tracking-wide hover:text-indigo-700">
              {{ product.category.name }}
            </Link>
            <span v-if="product.brand" class="text-xs text-gray-400">•</span>
            <Link v-if="product.brand" :href="`/products?brand=${product.brand.slug}`" class="text-xs text-gray-500 hover:text-gray-700">
              {{ product.brand.name }}
            </Link>
          </div>

          <!-- Name + Badges -->
          <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3 leading-tight">{{ product.name }}</h1>

          <div class="flex flex-wrap gap-2 mb-4">
            <span v-if="product.stock > 0" class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-green-50 text-green-700 text-xs font-semibold rounded-full">
              <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
              In Stock ({{ product.stock }} left)
            </span>
            <span v-else class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-50 text-rose-700 text-xs font-semibold rounded-full">
              <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
              Out of Stock
            </span>
          </div>

          <!-- Rating -->
          <a href="#reviews" class="flex items-center gap-2 mb-5 hover:opacity-80 transition-opacity w-fit">
            <StarRating :rating="product.avg_rating || 0" :count="product.reviews?.length || 0" size="md" :show-count="true" />
            <span class="text-sm text-gray-500">({{ product.reviews?.length || 0 }} reviews)</span>
          </a>

          <!-- Price -->
          <div class="flex items-baseline gap-3 mb-5">
            <span class="text-3xl font-extrabold text-gray-900">{{ currencySymbol }}{{ formatPrice(product.price) }}</span>
            <span v-if="product.compare_price && product.compare_price > product.price" class="text-xl text-gray-400 line-through">
              {{ currencySymbol }}{{ formatPrice(product.compare_price) }}
            </span>
            <span v-if="discountPercent > 0" class="px-2 py-0.5 bg-rose-100 text-rose-600 text-sm font-bold rounded-md">
              Save {{ discountPercent }}%
            </span>
          </div>

          <!-- Short Description -->
          <p v-if="product.short_description" class="text-gray-600 text-sm leading-relaxed mb-6">
            {{ product.short_description }}
          </p>

          <!-- Variants -->
          <div v-if="product.has_variants && product.attributes && product.attributes.length" class="mb-6 space-y-4">
            <div v-for="attr in product.attributes" :key="attr.id">
              <p class="text-sm font-semibold text-gray-700 mb-2">
                {{ attr.name }}:
                <span class="font-normal text-indigo-600">{{ selectedVariants[attr.id] || 'Select' }}</span>
              </p>
              <!-- Color swatches -->
              <div v-if="attr.type === 'color'" class="flex flex-wrap gap-2">
                <button
                  v-for="val in attr.values"
                  :key="val.id"
                  @click="selectVariant(attr.id, val.value)"
                  :title="val.label"
                  :class="[
                    'w-8 h-8 rounded-full border-2 transition-all',
                    selectedVariants[attr.id] === val.value ? 'border-indigo-500 scale-110 shadow-md' : 'border-transparent hover:border-gray-400'
                  ]"
                  :style="{ backgroundColor: val.color_code || val.value }"
                ></button>
              </div>
              <!-- Size/other buttons -->
              <div v-else class="flex flex-wrap gap-2">
                <button
                  v-for="val in attr.values"
                  :key="val.id"
                  @click="selectVariant(attr.id, val.value)"
                  :class="[
                    'px-3 py-1.5 text-sm font-medium rounded-lg border-2 transition-all',
                    selectedVariants[attr.id] === val.value
                      ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                      : 'border-gray-200 text-gray-700 hover:border-indigo-300'
                  ]"
                >
                  {{ val.label || val.value }}
                </button>
              </div>
            </div>
          </div>

          <!-- Quantity -->
          <div class="flex items-center gap-4 mb-6">
            <span class="text-sm font-semibold text-gray-700">Quantity:</span>
            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
              <button
                @click="quantity = Math.max(1, quantity - 1)"
                class="px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-indigo-600 font-bold transition-colors"
              >−</button>
              <span class="px-4 py-2.5 text-gray-900 font-semibold min-w-[3rem] text-center border-x border-gray-200">{{ quantity }}</span>
              <button
                @click="quantity = Math.min(product.stock || 99, quantity + 1)"
                class="px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-indigo-600 font-bold transition-colors"
              >+</button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 mb-6">
            <button
              @click="addToCart"
              :disabled="adding || product.stock <= 0"
              class="flex-1 py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2 text-base"
            >
              <svg v-if="!adding" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ adding ? 'Adding...' : (product.stock <= 0 ? 'Out of Stock' : 'Add to Cart') }}
            </button>
            <button
              @click="toggleWishlist"
              :class="[
                'px-4 py-3.5 rounded-xl border-2 font-semibold transition-all flex items-center gap-2',
                wishlisted
                  ? 'border-rose-500 bg-rose-50 text-rose-600 hover:bg-rose-100'
                  : 'border-gray-200 text-gray-600 hover:border-rose-300 hover:text-rose-500 hover:bg-rose-50'
              ]"
            >
              <svg class="w-5 h-5" :fill="wishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
              <span class="hidden sm:inline">{{ wishlisted ? 'Wishlisted' : 'Wishlist' }}</span>
            </button>
          </div>

          <!-- Meta Info -->
          <div class="border-t border-gray-100 pt-5 space-y-2">
            <div v-if="product.sku" class="flex gap-2 text-sm">
              <span class="text-gray-500 w-24 flex-shrink-0">SKU:</span>
              <span class="font-medium text-gray-700">{{ product.sku }}</span>
            </div>
            <div v-if="product.brand" class="flex gap-2 text-sm">
              <span class="text-gray-500 w-24 flex-shrink-0">Brand:</span>
              <Link :href="`/products?brand=${product.brand.slug}`" class="font-medium text-indigo-600 hover:text-indigo-800">{{ product.brand.name }}</Link>
            </div>
            <div v-if="product.weight" class="flex gap-2 text-sm">
              <span class="text-gray-500 w-24 flex-shrink-0">Weight:</span>
              <span class="font-medium text-gray-700">{{ product.weight }} kg</span>
            </div>
            <div v-if="product.dimensions" class="flex gap-2 text-sm">
              <span class="text-gray-500 w-24 flex-shrink-0">Dimensions:</span>
              <span class="font-medium text-gray-700">{{ product.dimensions }}</span>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="product.tags && product.tags.length" class="flex flex-wrap gap-2 mt-4">
            <Link
              v-for="tag in product.tags"
              :key="tag.id"
              :href="`/products?tags[]=${tag.slug}`"
              class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
            >
              #{{ tag.name }}
            </Link>
          </div>
        </div>
      </div>

      <!-- Tabs: Description + Reviews -->
      <div class="border border-gray-100 rounded-2xl overflow-hidden mb-12">
        <!-- Tab Headers -->
        <div class="flex border-b border-gray-100 bg-gray-50">
          <button
            @click="activeTab = 'description'"
            :class="[
              'px-6 py-4 text-sm font-semibold transition-colors',
              activeTab === 'description' ? 'text-indigo-600 border-b-2 border-indigo-600 bg-white' : 'text-gray-500 hover:text-gray-700'
            ]"
          >
            Description
          </button>
          <button
            id="reviews"
            @click="activeTab = 'reviews'"
            :class="[
              'px-6 py-4 text-sm font-semibold transition-colors flex items-center gap-2',
              activeTab === 'reviews' ? 'text-indigo-600 border-b-2 border-indigo-600 bg-white' : 'text-gray-500 hover:text-gray-700'
            ]"
          >
            Reviews
            <span class="bg-indigo-100 text-indigo-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ product.reviews?.length || 0 }}</span>
          </button>
        </div>

        <!-- Description Tab -->
        <div v-if="activeTab === 'description'" class="p-6 lg:p-8">
          <div v-if="product.description" class="prose prose-gray max-w-none text-sm leading-relaxed" v-html="product.description"></div>
          <p v-else class="text-gray-500 text-sm">No description available.</p>
        </div>

        <!-- Reviews Tab -->
        <div v-if="activeTab === 'reviews'" class="p-6 lg:p-8" id="reviews-section">
          <!-- Rating Summary -->
          <div v-if="product.reviews && product.reviews.length" class="flex flex-col sm:flex-row gap-8 mb-8 pb-8 border-b border-gray-100">
            <div class="text-center sm:text-left flex-shrink-0">
              <div class="text-5xl font-extrabold text-gray-900 mb-1">{{ (product.avg_rating || 0).toFixed(1) }}</div>
              <StarRating :rating="product.avg_rating || 0" size="lg" />
              <p class="text-sm text-gray-500 mt-1">{{ product.reviews.length }} reviews</p>
            </div>
            <div class="flex-1 space-y-2">
              <div v-for="stars in [5, 4, 3, 2, 1]" :key="stars" class="flex items-center gap-3">
                <span class="text-xs w-3 text-gray-500 text-right">{{ stars }}</span>
                <span class="text-yellow-400 text-xs">★</span>
                <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                  <div
                    class="h-full bg-yellow-400 rounded-full"
                    :style="{ width: `${getRatingPercent(stars)}%` }"
                  ></div>
                </div>
                <span class="text-xs text-gray-400 w-8">{{ getRatingCount(stars) }}</span>
              </div>
            </div>
          </div>

          <!-- Review List -->
          <div v-if="product.reviews && product.reviews.length" class="space-y-6 mb-8">
            <div v-for="review in product.reviews" :key="review.id" class="flex gap-4 pb-6 border-b border-gray-100 last:border-0 last:pb-0">
              <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-600">
                {{ (review.user?.name || 'A')[0].toUpperCase() }}
              </div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                  <span class="font-semibold text-gray-800 text-sm">{{ review.user?.name || 'Anonymous' }}</span>
                  <span v-if="review.verified_purchase" class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Verified Purchase
                  </span>
                  <span class="text-xs text-gray-400">{{ formatDate(review.created_at) }}</span>
                </div>
                <StarRating :rating="review.rating" size="sm" class="mb-2" />
                <p v-if="review.title" class="font-semibold text-gray-800 text-sm mb-1">{{ review.title }}</p>
                <p class="text-gray-600 text-sm leading-relaxed">{{ review.body }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <p class="text-gray-500 text-sm mb-2">No reviews yet. Be the first!</p>
          </div>

          <!-- Write Review Form -->
          <div v-if="auth?.user && !hasUserReviewed" class="border-t border-gray-100 pt-8">
            <h3 class="text-base font-bold text-gray-900 mb-4">Write a Review</h3>
            <form @submit.prevent="submitReview" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                <div class="flex gap-1">
                  <button
                    v-for="i in 5"
                    :key="i"
                    type="button"
                    @click="reviewForm.rating = i"
                    :class="['text-2xl transition-colors', i <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-200']"
                  >★</button>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Review Title</label>
                <input v-model="reviewForm.title" type="text" placeholder="Summarize your experience..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Review</label>
                <textarea v-model="reviewForm.body" rows="4" placeholder="Tell others what you think..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
              </div>
              <button type="submit" :disabled="reviewForm.processing" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-60 transition-colors text-sm">
                {{ reviewForm.processing ? 'Submitting...' : 'Submit Review' }}
              </button>
            </form>
          </div>
          <div v-else-if="!auth?.user" class="border-t border-gray-100 pt-6 text-center">
            <p class="text-sm text-gray-500 mb-3">Please sign in to write a review</p>
            <Link href="/login" class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
              Sign In
            </Link>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="related && related.length">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">You May Also Like</h2>
          <Link v-if="product.category" :href="`/products?category=${product.category.slug}`" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            View More <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          <ProductCard
            v-for="p in related"
            :key="p.id"
            :product="p"
            :currency-symbol="currencySymbol"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm, usePage } from '@inertiajs/vue3'
import ShopLayout from '../../../Layouts/ShopLayout.vue'
import ProductCard from '../../../Components/ProductCard.vue'
import StarRating from '../../../Components/StarRating.vue'

defineOptions({ layout: ShopLayout })

const props = defineProps({
  product: { type: Object, required: true },
  related: { type: Array, default: () => [] },
  isWishlisted: { type: Boolean, default: false }
})

const page = usePage()
const auth = computed(() => page.props.auth)
const currencySymbol = computed(() => page.props.currencySymbol || '$')

const activeImage = ref(props.product?.primaryImage || '')
const activeTab = ref('description')
const quantity = ref(1)
const adding = ref(false)
const wishlisted = ref(props.isWishlisted)
const selectedVariants = ref({})

const discountPercent = computed(() => {
  if (props.product.compare_price && props.product.compare_price > props.product.price) {
    return Math.round((1 - props.product.price / props.product.compare_price) * 100)
  }
  return 0
})

const hasUserReviewed = computed(() => {
  return props.product.reviews?.some(r => r.user_id === auth.value?.user?.id)
})

const reviewForm = useForm({
  product_id: props.product.id,
  rating: 5,
  title: '',
  body: ''
})

function formatPrice(price) {
  return Number(price).toFixed(2)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

function getRatingCount(stars) {
  return props.product.reviews?.filter(r => r.rating === stars).length || 0
}

function getRatingPercent(stars) {
  const total = props.product.reviews?.length || 0
  if (!total) return 0
  return Math.round((getRatingCount(stars) / total) * 100)
}

function selectVariant(attrId, value) {
  selectedVariants.value[attrId] = value
}

function addToCart() {
  adding.value = true
  router.post('/cart/add', {
    product_id: props.product.id,
    quantity: quantity.value,
    variants: selectedVariants.value
  }, {
    preserveScroll: true,
    onFinish: () => { adding.value = false }
  })
}

function toggleWishlist() {
  if (wishlisted.value) {
    router.delete(`/wishlist/${props.product.id}`, { preserveScroll: true })
    wishlisted.value = false
  } else {
    router.post('/wishlist', { product_id: props.product.id }, { preserveScroll: true })
    wishlisted.value = true
  }
}

function submitReview() {
  reviewForm.post(`/products/${props.product.slug}/reviews`, {
    preserveScroll: true,
    onSuccess: () => reviewForm.reset('title', 'body')
  })
}
</script>
