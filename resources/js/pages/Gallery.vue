<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Modal from '@/components/Modal.vue'
import WelcomeLayout from '@/layouts/WelcomeLayout.vue'

interface HotelImage {
  id: number
  title: string
  description: string | null
  image_path: string
  category: string
  is_featured: boolean
  sort_order: number
  is_active: boolean
  full_image_path: string
}

interface Props {
  images: Record<string, HotelImage[]>
}

const props = defineProps<Props>()



const selectedCategory = ref('all')
const selectedImage = ref<HotelImage | null>(null)

const categories: Record<string, string> = {
  all: 'All Images',
  rooms: 'Rooms & Suites',
  facilities: 'Hotel Facilities',
  attractions: 'Local Attractions',
  general: 'General'
}

const groupedImages = computed(() => props.images)

const featuredImages = computed(() => {
  const allImages = Object.values(props.images).flat()
  return allImages.filter(img => img && img.is_featured).slice(0, 6)
})

const categoryImages = computed(() => {
  if (selectedCategory.value === 'all') return []
  return props.images[selectedCategory.value] || []
})

const hasImages = computed(() => {
  return Object.values(props.images).some(category => Array.isArray(category) && category.length > 0)
})

const openModal = (image: HotelImage) => {
  selectedImage.value = image
}

const closeModal = () => {
  selectedImage.value = null
}

const isModalOpen = computed(() => selectedImage.value !== null)

const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement
  console.error('Image failed to load:', target.src)
  // Set a fallback image
  target.src = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop'
}

const handleImageLoad = (event: Event) => {
  const target = event.target as HTMLImageElement
  console.log('Image loaded successfully:', target.src)
}
</script>

<template>
  <WelcomeLayout>
    <Head>
      <title>Gallery - Dumindu Hotel</title>
      <meta name="description" content="Explore our stunning collection of hotel images showcasing rooms, facilities, and local attractions at Dumindu Hotel.">
    </Head>

    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Hotel Gallery</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
              Discover the beauty and comfort of Dumidu Hotel through our stunning collection of images showcasing our rooms, facilities, and local attractions.
            </p>
          </div>
        </div>
      </div>

      <!-- Category Navigation -->
      <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex space-x-8 overflow-x-auto py-4">
            <button
              v-for="(category, key) in categories"
              :key="key"
              @click="selectedCategory = key"
              :class="[
                'px-4 py-2 rounded-lg font-medium whitespace-nowrap transition duration-200',
                selectedCategory === key
                  ? 'bg-blue-600 text-white shadow-lg'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              ]"
            >
              {{ category }}
            </button>
          </div>
        </div>
      </div>

      <!-- Gallery Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Featured Images Section -->
        <div v-if="selectedCategory === 'all' && featuredImages.length > 0" class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Images</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="image in featuredImages"
              :key="image.id"
              class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 cursor-pointer"
              @click="openModal(image)"
            >
              <img
                :src="image.full_image_path"
                :alt="image.title"
                class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                @error="handleImageError"
                @load="handleImageLoad"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                  <h3 class="font-semibold text-lg mb-1">{{ image.title }}</h3>
                  <p v-if="image.description" class="text-sm text-gray-200">{{ image.description }}</p>
                </div>
              </div>
              <div class="absolute top-3 right-3">
                <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                  Featured
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Category Images -->
        <div v-if="selectedCategory !== 'all'">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ categories[selectedCategory] }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
              v-for="image in categoryImages"
              :key="image.id"
              class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 cursor-pointer"
              @click="openModal(image)"
            >
              <img
                :src="image.full_image_path"
                :alt="image.title"
                class="w-full h-48 object-cover group-hover:scale-110 transition duration-300"
                @error="handleImageError"
                @load="handleImageLoad"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                  <h3 class="font-semibold text-lg mb-1">{{ image.title }}</h3>
                  <p v-if="image.description" class="text-sm text-gray-200">{{ image.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- All Images Grid -->
        <div v-else-if="selectedCategory === 'all'" class="space-y-8">
          <div v-for="(images, category) in groupedImages" :key="category" class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-900">{{ categories[category] }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <div
                v-for="image in images"
                :key="image.id"
                class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 cursor-pointer"
                @click="openModal(image)"
              >
                <img
                  :src="image.full_image_path"
                  :alt="image.title"
                  class="w-full h-48 object-cover group-hover:scale-110 transition duration-300"
                  @error="handleImageError"
                  @load="handleImageLoad"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                  <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                    <h3 class="font-semibold text-lg mb-1">{{ image.title }}</h3>
                    <p v-if="image.description" class="text-sm text-gray-200">{{ image.description }}</p>
                  </div>
                </div>
                <div v-if="image.is_featured" class="absolute top-3 right-3">
                  <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                    Featured
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!hasImages" class="text-center py-16">
          <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-6">
            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No images available</h3>
          <p class="text-gray-500">Check back later for updates to our gallery.</p>
        </div>
      </div>

      <!-- Image Modal -->
      <Modal
        :show="isModalOpen"
        size="2xl"
        :show-close-button="true"
        @close="closeModal"
      >
        <div class="relative">
          <img
            v-if="selectedImage"
            :src="selectedImage.full_image_path"
            :alt="selectedImage.title"
            class="w-full h-auto max-h-[70vh] object-contain rounded-lg"
            @error="handleImageError"
            @load="handleImageLoad"
            loading="lazy"
          />
          <div v-if="selectedImage" class="mt-4">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ selectedImage.title }}</h3>
            <p v-if="selectedImage.description" class="text-gray-600">{{ selectedImage.description }}</p>
          </div>
        </div>
      </Modal>
    </div>
  </WelcomeLayout>
</template>

<style scoped>
    /* Custom color palette */
    .bg-gold-500 { background-color: #D4AF37; }
    .bg-gold-600 { background-color: #B8860B; }
    .text-gold-400 { color: #E6BF67; }
    .text-gold-500 { color: #D4AF37; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }

    /* Swiper custom styles */
    :deep(.swiper-pagination-bullet) {
        background: #D4AF37 !important;
        opacity: 0.5;
        width: 12px;
        height: 12px;
    }

    :deep(.swiper-pagination-bullet-active) {
        opacity: 1;
    }

    :deep(.swiper-button-next),
    :deep(.swiper-button-prev) {
        color: #D4AF37 !important;
        background: rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        backdrop-filter: blur(4px);
    }

    :deep(.swiper-button-next:after),
    :deep(.swiper-button-prev:after) {
        font-size: 24px !important;
        font-weight: bold;
    }

    /* Lightbox animations */
    .lightbox-enter-active,
    .lightbox-leave-active {
        transition: opacity 0.3s ease;
    }

    .lightbox-enter-from,
    .lightbox-leave-to {
        opacity: 0;
    }

    /* Grid item hover effect */
    .group:hover .aspect-\[4\/3\] {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }

    .aspect-\[4\/3\] {
        transition: transform 0.3s ease;
    }
</style>
