<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import Swal from 'sweetalert2';
    import AdminLayout from '@/layouts/AdminLayout.vue';
    import Modal from '@/components/Modal.vue'; // Added import for Modal component

    interface HotelImage {
        id: number;
        title: string;
        description: string | null;
        image_path: string;
        category: string;
        is_featured: boolean;
        sort_order: number;
        is_active: boolean;
        full_image_path: string;
    }

    interface Props {
        images: {
            data: HotelImage[];
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
            from: number;
            to: number;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        };
        categories: Record<string, string>;
    }

    const props = defineProps<Props>();



    const showUploadModal = ref(false);
    const showEditModal = ref(false);
    const uploading = ref(false);
    const updating = ref(false);
    const selectedCategory = ref('');
    const selectedImage = ref<HotelImage | null>(null);
    const fileInput = ref<HTMLInputElement>();

    const uploadForm = ref({
        title: '',
        description: '',
        category: '',
        image: null as File | null,
        is_featured: false,
        sort_order: 0
    });

    const editForm = ref({
        title: '',
        description: '',
        category: '',
        is_featured: false,
        is_active: true,
        sort_order: 0
    });

    const filteredImages = computed(() => {
        if (!selectedCategory.value) return props.images.data || [];
        return (props.images.data || []).filter(img => img && img.category === selectedCategory.value);
    });

    const handleFileSelect = (event: Event) => {
        const target = event.target as HTMLInputElement;
        if (target.files && target.files[0]) {
            uploadForm.value.image = target.files[0];
        }
    };

    const uploadImage = async () => {
        // Basic validation
        if (!uploadForm.value.title.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please enter a title for the image.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!uploadForm.value.category) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select a category for the image.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!uploadForm.value.image) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select an image file.',
                confirmButtonText: 'OK'
            });
            return;
        }

        uploading.value = true;

        try {
            const formData = new FormData();
            formData.append('title', uploadForm.value.title);
            formData.append('description', uploadForm.value.description);
            formData.append('category', uploadForm.value.category);
            formData.append('image', uploadForm.value.image);
            formData.append('is_featured', uploadForm.value.is_featured ? '1' : '0');
            formData.append('sort_order', uploadForm.value.sort_order.toString());

            console.log('Uploading image with data:', {
                title: uploadForm.value.title,
                description: uploadForm.value.description,
                category: uploadForm.value.category,
                is_featured: uploadForm.value.is_featured,
                sort_order: uploadForm.value.sort_order,
                image: uploadForm.value.image
            });

            console.log('Route name:', route('admin.gallery.images.store'));
            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(key, ':', value);
            }

            console.log('About to submit form...');
            
            // Use Inertia router with proper headers for API calls
            await router.post(route('admin.gallery.images.store'), formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                onSuccess: (response) => {
                    console.log('Upload successful:', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Image Uploaded',
                        text: 'Image has been uploaded successfully.',
                        confirmButtonText: 'OK'
                    });
                    showUploadModal.value = false;
                    resetUploadForm();
                },
                onError: (errors) => {
                    console.error('Upload errors:', errors);
                    let errorMessage = 'Failed to upload image. Please try again.';
                    
                    if (errors && Object.keys(errors).length > 0) {
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });

        } catch (error: any) {
            console.error('Upload error:', error);
            let errorMessage = 'Failed to upload image. Please try again.';
            
            if (error.response?.data?.errors) {
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('\n');
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        } finally {
            uploading.value = false;
        }
    };

    const editImage = (image: HotelImage) => {
        selectedImage.value = image;
        editForm.value = {
            title: image.title,
            description: image.description || '',
            category: image.category,
            is_featured: image.is_featured,
            is_active: image.is_active,
            sort_order: image.sort_order
        };
        showEditModal.value = true;
    };

    const updateImage = async () => {
        if (!selectedImage.value) return;

        // Basic validation
        if (!editForm.value.title.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please enter a title for the image.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!editForm.value.category) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select a category for the image.',
                confirmButtonText: 'OK'
            });
            return;
        }

        updating.value = true;

        try {
            // Convert boolean values to proper format for the backend
            const formData = new FormData();
            formData.append('title', editForm.value.title);
            formData.append('description', editForm.value.description);
            formData.append('category', editForm.value.category);
            formData.append('is_featured', editForm.value.is_featured ? '1' : '0');
            formData.append('is_active', editForm.value.is_active ? '1' : '0');
            formData.append('sort_order', editForm.value.sort_order.toString());

            console.log('Updating image with data:');
            for (let [key, value] of formData.entries()) {
                console.log(key, ':', value);
            }

            await router.put(route('admin.gallery.images.update', selectedImage.value.id), formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                onSuccess: (response) => {
                    console.log('Update successful:', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Image Updated',
                        text: 'Image has been updated successfully.',
                        confirmButtonText: 'OK'
                    });
                    showEditModal.value = false;
                    selectedImage.value = null;
                },
                onError: (errors) => {
                    console.error('Update errors:', errors);
                    let errorMessage = 'Failed to update image. Please try again.';
                    
                    if (errors && Object.keys(errors).length > 0) {
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });

        } catch (error: any) {
            console.error('Update error:', error);
            let errorMessage = 'Failed to update image. Please try again.';
            
            if (error.response?.data?.errors) {
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('\n');
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        } finally {
            updating.value = false;
        }
    };

    const deleteImage = async (image: HotelImage) => {
        const result = await Swal.fire({
            icon: 'warning',
            title: 'Delete Image',
            text: `Are you sure you want to delete "${image.title}"? This action cannot be undone.`,
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it'
        });

        if (result.isConfirmed) {
            try {
                await router.delete(route('admin.gallery.images.destroy', image.id), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    onSuccess: (response) => {
                        console.log('Delete successful:', response);
                        Swal.fire({
                            icon: 'success',
                            title: 'Image Deleted',
                            text: 'Image has been deleted successfully.',
                            confirmButtonText: 'OK'
                        });
                    },
                    onError: (errors) => {
                        console.error('Delete errors:', errors);
                        let errorMessage = 'Failed to delete image. Please try again.';
                        
                        if (errors && Object.keys(errors).length > 0) {
                            errorMessage = Object.values(errors).flat().join('\n');
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Delete Failed',
                            text: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    }
                });

            } catch (error: any) {
                console.error('Delete error:', error);
                let errorMessage = 'Failed to delete image. Please try again.';
                
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        }
    };

    const toggleFeatured = async (image: HotelImage) => {
        try {
            await router.put(route('admin.gallery.images.toggle-featured', image.id), {}, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                onSuccess: (response) => {
                    console.log('Toggle featured successful:', response);
                },
                onError: (errors) => {
                    console.error('Toggle featured errors:', errors);
                    let errorMessage = 'Failed to toggle featured status.';
                    
                    if (errors && Object.keys(errors).length > 0) {
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Toggle Failed',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });
        } catch (error: any) {
            console.error('Failed to toggle featured status:', error);
            let errorMessage = 'Failed to toggle featured status.';
            
            if (error.response?.data?.errors) {
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('\n');
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Toggle Failed',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    };

    const toggleActive = async (image: HotelImage) => {
        try {
            await router.put(route('admin.gallery.images.toggle-active', image.id), {}, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                onSuccess: (response) => {
                    console.log('Toggle active successful:', response);
                },
                onError: (errors) => {
                    console.error('Toggle active errors:', errors);
                    let errorMessage = 'Failed to toggle active status.';
                    
                    if (errors && Object.keys(errors).length > 0) {
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Toggle Failed',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });
        } catch (error: any) {
            console.error('Failed to toggle active status:', error);
            let errorMessage = 'Failed to toggle active status.';
            
            if (error.response?.data?.errors) {
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('\n');
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Toggle Failed',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    };

    const resetUploadForm = () => {
        uploadForm.value = {
            title: '',
            description: '',
            category: '',
            image: null,
            is_featured: false,
            sort_order: 0
        };
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    };

    const handleImageError = (event: Event) => {
        const target = event.target as HTMLImageElement;
        console.error('Image failed to load:', target.src);
        // Set a fallback image
        target.src = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop';
    };

    const handleImageLoad = (event: Event) => {
        const target = event.target as HTMLImageElement;
        console.log('Image loaded successfully:', target.src);
    };
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-gray-50">
            <Head title="Gallery Management" />

            <!-- Header -->
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Gallery Management</h1>
                            <p class="text-gray-600">Manage hotel images and visual content</p>
                        </div>
                        <button
                            @click="showUploadModal = true"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200"
                        >
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Upload Image
                        </button>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Hotel Images</h3>
                            <div class="flex space-x-2">
                                <select
                                    v-model="selectedCategory"
                                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="(label, key) in categories" :key="key" :value="key">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div
                                v-for="image in filteredImages"
                                :key="image.id"
                                class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200"
                            >
                                <!-- Image -->
                                <div class="aspect-w-4 aspect-h-3">
                                    <img
                                        :src="image.full_image_path"
                                        :alt="image.title"
                                        class="w-full h-48 object-cover group-hover:scale-105 transition duration-200"
                                        @error="handleImageError"
                                        @load="handleImageLoad"
                                        loading="lazy"
                                    />
                                </div>

                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition duration-200">
                                    <div class="absolute top-3 right-3 flex space-x-2">
                                        <button
                                            @click="editImage(image)"
                                            class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition duration-200"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteImage(image)"
                                            class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition duration-200"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Image Info -->
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-900 truncate">{{ image.title }}</h4>
                                        <div class="flex space-x-1">
                                            <button
                                                @click="toggleFeatured(image)"
                                                :class="[
                                                    'p-1 rounded text-xs',
                                                    image.is_featured
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-gray-100 text-gray-600 hover:bg-yellow-100 hover:text-yellow-800'
                                                ]"
                                                :title="image.is_featured ? 'Remove from featured' : 'Mark as featured'"
                                            >
                                                ⭐
                                            </button>
                                            <button
                                                @click="toggleActive(image)"
                                                :class="[
                                                    'p-1 rounded text-xs',
                                                    image.is_active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800'
                                                ]"
                                                :title="image.is_active ? 'Deactivate' : 'Activate'"
                                            >
                                                {{ image.is_active ? '✓' : '✗' }}
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <p v-if="image.description" class="text-sm text-gray-600 mb-2 line-clamp-2">
                                        {{ image.description }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="capitalize">{{ image.category }}</span>
                                        <span>Order: {{ image.sort_order }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredImages.length === 0" class="text-center py-16">
                            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-100 mb-6">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No images found</h3>
                            <p class="text-gray-500">Upload your first image to get started.</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="images.links && images.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ images.from }} to {{ images.to }} of {{ images.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in images.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md',
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : link.url
                                            ? 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'
                                            : 'text-gray-400 cursor-not-allowed'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Modal -->
            <Modal
                :show="showUploadModal"
                title="Upload New Image"
                size="md"
                @close="showUploadModal = false"
            >
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Image Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="uploadForm.title"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter image title"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="uploadForm.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter image description"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="uploadForm.category"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select category</option>
                            <option v-for="(label, key) in categories" :key="key" :value="key">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Image File <span class="text-red-500">*</span>
                        </label>
                        <input
                            ref="fileInput"
                            type="file"
                            required
                            accept="image/*"
                            @change="handleFileSelect"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <p class="text-xs text-gray-500 mt-1">Max size: 5MB. Formats: JPEG, PNG, JPG, GIF</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input
                            v-model="uploadForm.is_featured"
                            type="checkbox"
                            id="featured"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="featured" class="text-sm text-gray-700">Mark as featured</label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input
                            v-model="uploadForm.sort_order"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0"
                        />
                    </div>
                </div>

                <template #footer>
                    <button
                        type="button"
                        @click="showUploadModal = false"
                        class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="uploadImage"
                        :disabled="uploading"
                        class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="uploading" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </span>
                        <span v-else>Upload Image</span>
                    </button>
                </template>
            </Modal>

            <!-- Edit Modal -->
            <Modal
                :show="showEditModal"
                title="Edit Image"
                size="md"
                @close="showEditModal = false"
            >
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Image Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="editForm.title"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            v-model="editForm.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="editForm.category"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option v-for="(label, key) in categories" :key="key" :value="key">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input
                            v-model="editForm.is_featured"
                            type="checkbox"
                            id="edit-featured"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="edit-featured" class="text-sm text-gray-700">Mark as featured</label>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input
                            v-model="editForm.is_active"
                            type="checkbox"
                            id="edit-active"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="edit-active" class="text-sm text-gray-700">Active</label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input
                            v-model="editForm.sort_order"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <template #footer>
                    <button
                        type="button"
                        @click="showEditModal = false"
                        class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="updateImage"
                        :disabled="updating"
                        class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="updating" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                        <span v-else>Update Image</span>
                    </button>
                </template>
            </Modal>
        </div>
    </AdminLayout>
</template>

<style>
    /* Custom gold color for Tailwind */
    .bg-gold-100 { background-color: #fff9e6; }
    .bg-gold-200 { background-color: #ffedb3; }
    .bg-gold-500 { background-color: #ffd700; }
    .bg-gold-600 { background-color: #e6c200; }
    .bg-gold-700 { background-color: #ccad00; }
    .text-gold-600 { color: #e6c200; }
    .text-gold-800 { color: #997a00; }
    .border-gold-500 { border-color: #ffd700; }
    .border-gold-600 { border-color: #e6c200; }
    .hover\:bg-gold-700:hover { background-color: #ccad00; }
    .hover\:text-gold-800:hover { color: #997a00; }
    .focus\:ring-gold-500:focus { --tw-ring-color: #ffd700; }
</style>
