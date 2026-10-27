import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import type { AppLayoutProps } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { notif } from '@/utils/sweetAlert';
import { TooltipProvider } from '@/components/ui/tooltip'; // 1. Import ini

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => {
    const { flash } = usePage().props as any;

    useEffect(() => {
        if (flash?.success) {
            notif.success(flash.success);
        }

        if (flash?.error) {
            notif.error(flash.error);
        }
    }, [flash]);

    return (
        /* 2. Bungkus semua dengan TooltipProvider */
        <TooltipProvider delayDuration={0}>
            <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
                {children}
            </AppLayoutTemplate>
        </TooltipProvider>
    );
};
