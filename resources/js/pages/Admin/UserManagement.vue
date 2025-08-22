<template>
    <AdminLayout>
        <!-- Filters and Controls -->
        <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 mb-4 lg:mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="w-full lg:w-auto">
                    <h2 class="text-xl lg:text-2xl font-bold text-blue-900">Users Management</h2>
                    <p class="text-blue-700 text-sm lg:text-base">Manage user accounts, roles, and access</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <!-- Role Filter -->
                    <div class="w-full sm:w-48">
                        <Select
                            v-model="roleFilter"
                            :options="roleOptions"
                            placeholder="Select role..."
                            label="User Role"
                        />
                    </div>

                    <!-- Date Range Filter -->
                    <div class="w-full sm:w-64">
                        <DatePicker
                            v-model="dateRange"
                            :range="true"
                            placeholder="Filter by date range..."
                            label="Date Range"
                            format="dd/MM/yyyy"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end items-center mb-4 lg:mb-6">
            <button
                @click="openUserModal(null)"
                class="w-full sm:w-auto bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-4 lg:px-6 rounded-full transition flex items-center justify-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden sm:inline">Add New User</span>
                <span class="sm:hidden">Add User</span>
            </button>
        </div>

        <!-- Users DataTable -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <DataTable
            :data="filteredUsers"
            :columns="tableColumns"
            :show-search="true"
            :show-filters="false"
            :show-pagination="true"
            search-placeholder="Search users..."
            :page-size="10"
        >
            <!-- Custom cell templates -->
            <template #cell-name="{ row }">
                    <div class="min-w-0">
                        <div class="font-medium text-blue-900 truncate">{{ row.original.name }}</div>
                        <div class="text-sm text-blue-700 truncate">ID: {{ row.original.id }}</div>
                </div>
            </template>

            <template #cell-email="{ row }">
                    <div class="min-w-0">
                        <div class="text-blue-900 truncate">{{ row.original.email }}</div>
                        <div class="text-sm text-blue-700 truncate">{{ row.original.phone || 'No phone' }}</div>
                </div>
            </template>

            <template #cell-role="{ value }">
                    <span class="bg-blue-100 text-blue-900 text-xs font-semibold px-2 py-1 rounded whitespace-nowrap">
                    {{ capitalizeFirstLetter(value) }}
                </span>
            </template>

            <template #cell-email_verified_at="{ value }">
                <div v-if="value" class="flex items-center text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-sm">Verified</span>
                </div>
                <div v-else class="text-sm text-gray-500">Not verified</div>
            </template>

            <template #cell-created_at="{ value }">
                    <span class="text-sm text-gray-600 whitespace-nowrap">{{ formatDate(value) }}</span>
            </template>

            <template #cell-actions="{ row }">
                    <div class="flex justify-end space-x-1 lg:space-x-2">
                    <button
                        @click="openUserModal(row.original)"
                            class="text-blue-600 hover:text-blue-800 p-1 lg:p-2 rounded hover:bg-blue-50 transition-colors"
                            title="Edit user"
                    >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button
                        @click="deleteUser(row.original.id)"
                            class="text-red-600 hover:text-red-800 p-1 lg:p-2 rounded hover:bg-red-50 transition-colors"
                            title="Delete user"
                    >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Empty state -->
            <template #empty>
                    <div class="text-center py-8 lg:py-16 px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 lg:h-16 lg:w-16 mx-auto text-blue-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                        <h3 class="text-xl lg:text-2xl font-bold text-blue-900 mb-2">No Users Found</h3>
                        <p class="text-blue-700 mb-6 text-sm lg:text-base">Try adjusting your filters or add a new user</p>
                    <button
                        @click="openUserModal(null)"
                            class="bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 lg:px-8 rounded-full transition"
                    >
                        Add New User
                    </button>
                </div>
            </template>
        </DataTable>
        </div>

        <!-- Stats Summary with Charts -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mt-6 lg:mt-8">
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-t-4 border-blue-500">
                <div class="text-2xl lg:text-3xl font-bold text-blue-900 mb-2">{{ users.length }}</div>
                <div class="text-blue-700 text-sm lg:text-base">Total Users</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-t-4 border-gold-500">
                <div class="text-2xl lg:text-3xl font-bold text-blue-900 mb-2">{{ adminUsersCount }}</div>
                <div class="text-blue-700 text-sm lg:text-base">Admin Users</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-t-4 border-purple-500 sm:col-span-2 lg:col-span-1">
                <div class="text-2xl lg:text-3xl font-bold text-blue-900 mb-2">{{ verifiedUsersCount }}</div>
                <div class="text-blue-700 text-sm lg:text-base">Verified Users</div>
            </div>
        </div>

        <!-- User Statistics Chart -->
        <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 mt-6 lg:mt-8">
            <h3 class="text-lg lg:text-xl font-bold text-blue-900 mb-4">User Statistics</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Roles Chart -->
                <div class="min-h-[300px]">
                <Chart
                    type="doughnut"
                    :data="userRolesChartData"
                    :options="chartOptions"
                    title="User Roles Distribution"
                    height="300"
                />
                </div>
                
                <!-- User Registration Trend -->
                <div class="min-h-[300px]">
                <Chart
                    type="line"
                    :data="userRegistrationChartData"
                    :options="lineChartOptions"
                    title="User Registration Trend"
                    height="300"
                />
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <Modal
            v-model:show="showUserModal"
            :title="currentUser.id ? 'Edit User' : 'Add New User'"
            size="lg"
            @close="closeUserModal"
        >
            <form @submit.prevent="saveUser">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Full Name</label>
                        <input
                            type="text"
                            v-model="currentUser.name"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500 text-sm lg:text-base"
                            placeholder="John Doe"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Email</label>
                        <input
                            type="email"
                            v-model="currentUser.email"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500 text-sm lg:text-base"
                            placeholder="user@example.com"
                            required
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Phone Number</label>
                        <input
                            type="tel"
                            v-model="currentUser.phone"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500 text-sm lg:text-base"
                            placeholder="+1234567890"
                        >
                    </div>
                    <div>
                        <Select
                            v-model="currentUser.role"
                            :options="roleOptions"
                            placeholder="Select role..."
                            label="User Role"
                            required
                        />
                    </div>
                </div>

                <div v-if="!currentUser.id" class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Password</label>
                        <input
                            type="password"
                            v-model="currentUser.password"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500 text-sm lg:text-base"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-900 mb-2">Confirm Password</label>
                        <input
                            type="password"
                            v-model="currentUser.password_confirmation"
                            class="w-full border border-blue-200 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-gold-500 text-sm lg:text-base"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>
            </form>

            <template #footer>
                <button
                    type="button"
                    @click="closeUserModal"
                    class="w-full sm:w-auto bg-white border border-blue-300 text-blue-700 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg transition"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    @click="saveUser"
                    class="w-full sm:w-auto bg-gold-500 hover:bg-gold-600 text-blue-900 font-bold py-3 px-6 rounded-lg transition"
                >
                    {{ currentUser.id ? 'Update User' : 'Create User' }}
                </button>
            </template>
        </Modal>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="fixed top-4 right-4 left-4 sm:left-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg z-50 shadow-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="flex-1 text-sm">{{ errorMessage }}</span>
                <button @click="errorMessage = ''" class="ml-4 text-red-700 hover:text-red-900 p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Success Alert -->
        <div v-if="successMessage" class="fixed top-4 right-4 left-4 sm:left-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg z-50 shadow-lg">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="flex-1 text-sm">{{ successMessage }}</span>
                <button @click="successMessage = ''" class="ml-4 text-green-700 hover:text-green-900 p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
    import { ref, computed, reactive, onMounted } from 'vue';
    import { type ColumnDef } from '@tanstack/vue-table';
    import axios from 'axios';
    import AdminLayout from '@/layouts/AdminLayout.vue';
    import { DataTable, Select, DatePicker, Chart, Modal } from '@/components/ui';

    interface User {
        id: number;
        name: string;
        email: string;
        phone?: string;
        role: string;
        email_verified_at?: string;
        created_at: string;
        updated_at: string;
    }

    interface CurrentUser {
        id: number | null;
        name: string;
        email: string;
        phone: string;
        role: string;
        password: string;
        password_confirmation: string;
    }

    const users = ref<User[]>([]);
    const errorMessage = ref('');
    const successMessage = ref('');

    // New reactive data for plugins
    const roleFilter = ref("all");
    const dateRange = ref<[Date, Date] | null>(null);

    // Role options for Select component
    const roleOptions = [
        { value: 'all', label: 'All Roles' },
        { value: 'guest', label: 'Guest' },
        { value: 'user', label: 'User' },
        { value: 'admin', label: 'Admin' }
    ];

    // Table columns definition
    const tableColumns: ColumnDef<User>[] = [
        {
            accessorKey: 'name',
            header: 'User',
        },
        {
            accessorKey: 'email',
            header: 'Contact',
        },
        {
            accessorKey: 'role',
            header: 'Role',
        },
        {
            accessorKey: 'email_verified_at',
            header: 'Email Verified',
        },
        {
            accessorKey: 'created_at',
            header: 'Joined',
        },
        {
            id: 'actions',
            header: 'Actions',
        },
    ];

    // Chart data
    const userRolesChartData = computed(() => ({
        labels: ['Guest', 'User', 'Admin'],
        datasets: [
            {
                data: [
                    users.value.filter(u => u.role === 'guest').length,
                    users.value.filter(u => u.role === 'user').length,
                    users.value.filter(u => u.role === 'admin').length,
                ],
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                borderWidth: 0,
            },
        ],
    }));

    const userRegistrationChartData = computed(() => {
        const last6Months = Array.from({ length: 6 }, (_, i) => {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }).reverse();

        const registrationCounts = last6Months.map(month => {
            return users.value.filter(user => {
                const userDate = new Date(user.created_at);
                return userDate.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) === month;
            }).length;
        });

        return {
            labels: last6Months,
            datasets: [
                {
                    label: 'New Users',
                    data: registrationCounts,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                },
            ],
        };
    });

    // Chart options
    const chartOptions = {
        plugins: {
            legend: {
                position: 'bottom' as const,
            },
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    const lineChartOptions = {
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                },
            },
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    const loadUsers = async () => {
        try {
            const response = await axios.get('/users');
            
            // The API now returns a direct array
            if (Array.isArray(response.data)) {
                users.value = response.data;
            } else {
                console.error('Unexpected response structure:', response.data);
                users.value = [];
                errorMessage.value = 'Invalid data format received from server.';
            }
        } catch (error) {
            console.error('Failed to load users:', error);
            errorMessage.value = 'Failed to load users. Please try again.';
            users.value = [];
        }
    };

    onMounted(loadUsers);

    // Modal
    const showUserModal = ref(false);

    // Current user for editing
    const currentUser = reactive<CurrentUser>({
        id: null,
        name: "",
        email: "",
        phone: "",
        role: "guest",
        password: "",
        password_confirmation: ""
    });

    // Filtered users with enhanced filtering
    const filteredUsers = computed(() => {
        if (!Array.isArray(users.value)) {
            console.warn('users.value is not an array:', users.value);
            return [];
        }
        
        return users.value.filter(user => {
            const matchesRole = roleFilter.value === "all" || user.role === roleFilter.value;
            
            // Date range filtering
            let matchesDateRange = true;
            if (dateRange.value && dateRange.value[0] && dateRange.value[1]) {
                const userDate = new Date(user.created_at);
                const startDate = dateRange.value[0];
                const endDate = dateRange.value[1];
                matchesDateRange = userDate >= startDate && userDate <= endDate;
            }

            return matchesRole && matchesDateRange;
        });
    });

    // Stats
    const adminUsersCount = computed(() => {
        if (!Array.isArray(users.value)) return 0;
        return users.value.filter(u => u.role === "admin").length;
    });

    const verifiedUsersCount = computed(() => {
        if (!Array.isArray(users.value)) return 0;
        return users.value.filter(u => u.email_verified_at !== null).length;
    });

    // Format date for display
    const formatDate = (dateString: string) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    };

    // Capitalize first letter
    const capitalizeFirstLetter = (string: string) => {
        if (string === 'user') return 'Guest';
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    // Modal handlers
    const openUserModal = (user: User | null) => {
        if (user) {
            Object.assign(currentUser, {
                id: user.id,
                name: user.name,
                email: user.email,
                phone: user.phone || '',
                role: user.role,
                password: '',
                password_confirmation: ''
            });
        } else {
            resetCurrentUser();
        }
        showUserModal.value = true;
    };

    const closeUserModal = () => {
        showUserModal.value = false;
        errorMessage.value = '';
    };

    const resetCurrentUser = () => {
        currentUser.id = null;
        currentUser.name = "";
        currentUser.email = "";
        currentUser.phone = "";
        currentUser.role = "guest";
        currentUser.password = "";
        currentUser.password_confirmation = "";
    };

    // CRUD Operations
    const saveUser = async () => {
        try {
            errorMessage.value = '';
            
            if (currentUser.id) {
                // Update existing user
                const updateData = {
                    name: currentUser.name,
                    email: currentUser.email,
                    phone: currentUser.phone,
                    role: currentUser.role
                };
                await axios.put(`/users/${currentUser.id}`, updateData);
                successMessage.value = 'User updated successfully!';
            } else {
                // Create new user
                const createData = {
                    name: currentUser.name,
                    email: currentUser.email,
                    phone: currentUser.phone,
                    role: currentUser.role,
                    password: currentUser.password,
                    password_confirmation: currentUser.password_confirmation
                };
                await axios.post('/users', createData);
                successMessage.value = 'User created successfully!';
            }
            
            await loadUsers();
            showUserModal.value = false;
            
            // Clear success message after 3 seconds
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        } catch (error: any) {
            console.error('Save user failed:', error);
            if (error.response?.data?.message) {
                errorMessage.value = error.response.data.message;
            } else if (error.response?.data?.errors) {
                const errors = error.response.data.errors;
                errorMessage.value = Object.values(errors).flat().join(', ');
            } else {
                errorMessage.value = 'Failed to save user. Please try again.';
            }
        }
    };

    const deleteUser = async (id: number) => {
        if (confirm("Are you sure you want to delete this user?")) {
            try {
                await axios.delete(`/users/${id}`);
                await loadUsers();
                successMessage.value = 'User deleted successfully!';
                
                // Clear success message after 3 seconds
                setTimeout(() => {
                    successMessage.value = '';
                }, 3000);
            } catch (error: any) {
                console.error('Delete user failed:', error);
                errorMessage.value = 'Failed to delete user. Please try again.';
            }
        }
    };
</script>

<style scoped>
    /* Custom color palette */
    .bg-gold-500 { background-color: #D4AF37; }
    .bg-gold-600 { background-color: #B8860B; }
    .text-gold-500 { color: #D4AF37; }
    .bg-blue-900 { background-color: #0C2D6A; }
    .text-blue-900 { color: #0C2D6A; }
    .text-blue-700 { color: #1A4480; }
    .bg-blue-50 { background-color: #F0F5FF; }
</style>
