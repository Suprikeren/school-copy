import { Config } from 'ziggy-js';
import { Role } from './role';

export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    referral_code: string | null;
    email_verified_at?: string;
    avatar?: string;
    roles: Role[];
}

export interface UserLink {
    url: string | null;
    label: string;
    active: boolean;
}

// Tambahkan export pada tipe ini
export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null; // Gunakan | null agar aman saat logout/guest
        permissions: Record<string, boolean>;
        super: boolean;
    };
    ziggy: Config & { location: string };
    flash: { // Tambahkan ini sekalian buat SweetAlert
        success: string | null;
        error: string | null;
    };
    [key: string]: any;
};
