<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="text-xs font-medium text-gray-500 uppercase tracking-wider">Navigation</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child 
                    :is-active="item.href === page.url" 
                    :tooltip="item.title"
                    class="group transition-colors duration-200"
                >
                    <Link 
                        :href="item.href"
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                        :class="{
                            'bg-blue-50 text-blue-700 border-r-2 border-blue-500': item.href === page.url,
                            'text-gray-700 hover:text-gray-900': item.href !== page.url
                        }"
                    >
                        <component 
                            :is="item.icon" 
                            class="h-5 w-5 flex-shrink-0"
                            :class="{
                                'text-blue-600': item.href === page.url,
                                'text-gray-400 group-hover:text-gray-600': item.href !== page.url
                            }"
                        />
                        <span class="font-medium text-sm">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
