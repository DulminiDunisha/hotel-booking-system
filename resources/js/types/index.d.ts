import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Guest {
    id: number;
    name: string;
    email: string;
    phone?: string;
    role?: string;
    created_at: string;
    updated_at: string;
}

export interface Room {
    id: number;
    name: string;
    type: string;
    number: string;
    base_price: number;
    description?: string;
    capacity: number;
    created_at: string;
    updated_at: string;
}

export interface Booking {
    id: number;
    booking_code: string;
    guest_id: number;
    room_id: number;
    check_in_date: string;
    check_out_date: string;
    adults: number;
    children: number;
    payment_method: string;
    payment_status: string;
    status: string;
    total_amount: number;
    nights?: number;
    guest?: Guest;
    room?: Room;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
