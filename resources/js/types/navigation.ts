import type { InertiaLinkProps } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';

export type BreadcrumbItem = {
    title: string;
    href: string;
};

export type NavItem = {
    title: string;
    // href: NonNullable<InertiaLinkProps['href']>;
    href: any;
    // icon?: LucideIcon | null;
    icon?: any;
    isActive?: boolean;
    items?: {
        title: string;
        href: any; // Samakan di sini
        icon: any;
    }[];
};
