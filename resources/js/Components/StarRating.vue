<template>
  <div class="flex items-center" :class="gapClass">
    <div class="flex items-center">
      <span
        v-for="i in 5"
        :key="i"
        :class="[starSizeClass, getStarColor(i)]"
        class="leading-none select-none"
      >
        <template v-if="i <= fullStars">★</template>
        <template v-else-if="i === fullStars + 1 && hasHalf">
          <span class="relative inline-block">
            <span class="text-gray-300">★</span>
            <span class="absolute inset-0 overflow-hidden w-1/2">
              <span class="text-yellow-400">★</span>
            </span>
          </span>
        </template>
        <template v-else>☆</template>
      </span>
    </div>
    <span v-if="showCount && count !== undefined" :class="countSizeClass" class="text-gray-500 font-medium">
      ({{ count.toLocaleString() }})
    </span>
    <span v-if="showRating" :class="countSizeClass" class="text-gray-600 font-semibold">
      {{ rating.toFixed(1) }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  rating: {
    type: Number,
    default: 0
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v)
  },
  showCount: {
    type: Boolean,
    default: false
  },
  showRating: {
    type: Boolean,
    default: false
  },
  count: {
    type: Number,
    default: 0
  }
})

const fullStars = computed(() => Math.floor(props.rating))
const hasHalf = computed(() => (props.rating % 1) >= 0.4)

const starSizeClass = computed(() => ({
  xs: 'text-xs',
  sm: 'text-sm',
  md: 'text-base',
  lg: 'text-xl',
  xl: 'text-2xl'
}[props.size] || 'text-base'))

const countSizeClass = computed(() => ({
  xs: 'text-xs',
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base',
  xl: 'text-lg'
}[props.size] || 'text-sm'))

const gapClass = computed(() => ({
  xs: 'gap-0.5',
  sm: 'gap-1',
  md: 'gap-1',
  lg: 'gap-1.5',
  xl: 'gap-2'
}[props.size] || 'gap-1'))

function getStarColor(i) {
  if (i <= fullStars.value) return 'text-yellow-400'
  if (i === fullStars.value + 1 && hasHalf.value) return ''
  return 'text-gray-300'
}
</script>
