// import type { Auth } from '@/types/auth';

// declare module '@inertiajs/core' {
//     export interface InertiaConfig {
//         sharedPageProps: {
//             name: string;
//             auth: Auth;
//             sidebarOpen: boolean;
//             [key: string]: unknown;
//         };
//     }
// }

import type { Auth } from '@/types/auth';
import type { route as routeFn } from 'ziggy-js';

declare global {
    const route: typeof routeFn;
}


declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
