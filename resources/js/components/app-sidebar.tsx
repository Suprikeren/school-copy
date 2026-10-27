import { Link, usePage } from '@inertiajs/react';
import {
    BadgeDollarSign,
    BookOpen,
    Box,
    Building2,
    ClipboardList,
    Contact,
    CreditCard,
    Database,
    Folder,
    LayoutGrid,
    MonitorPlay,
    Receipt,
    Tags,
    History as HistoryIcon,
    UserCog,
    Users,
    Zap,
    ShieldCheck,
    Key,
    Settings2,
    TicketPercent,
    Layers,
    Factory,
    UserCheck,
    GraduationCap,
} from 'lucide-react';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
// import { route } from 'ziggy-js';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarTrigger,
} from '@/components/ui/sidebar';
// import type { NavItem } from '@/types';
import type { NavItem } from '@/types/navigation';
import AppLogo from './app-logo';
// import { route } from 'ziggy-js';
// import { dashboard } from '@/routes';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '#',
        // href: route('dashboard.index'),
        icon: LayoutGrid,
    },
];

// super admin

const usersNavItems: NavItem[] = [
    {
        title: 'Users Management',
        href: '#',
        icon: UserCog,
        isActive: false,
        items: [
            {
                title: 'Users',
                href: route('dashboard.users.index'),
                icon: Users,
            },

            {
                title: 'Roles',
                href: route('dashboard.roles.index'),
                icon: ShieldCheck,
            },
            {
                title: 'Permissions',
                href: route('dashboard.permissions.index'),
                icon: Key,
            },
        ],
    },
];


// super -admin selesai

// admin / tenant selesai

const tatibNavItems: NavItem[] = [
    {
        title: 'Pasal Tatib',
        href: route('dashboard.tatib.pasal.index'),
        icon: Receipt,
    },
    {
        title: 'Jenis Pelanggaran',
        href: route('dashboard.tatib.jenis-pelanggaran.index'),
        icon: ClipboardList,
    },
];

const dataNavItems: NavItem[] = [
    {
        title: 'Siswa',
        href: route('dashboard.siswa.index'),
        icon: Users,
    },
    {
        title: 'Riwayat Kelas',
        href: route('dashboard.riwayat-kelas.index'),
        icon: GraduationCap,
    },
    {
        title: 'Input Pelanggaran',
        href: route('dashboard.pelanggaran.index'),
        icon: ClipboardList,
    },
    {
        title: 'Tindak Lanjut',
        href: route('dashboard.tindak-lanjut.index'),
        icon: Contact,
    },
    {
        title: 'Pengajuan Perubahan',
        href: route('dashboard.pengajuan.index'),
        icon: HistoryIcon,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];


export function AppSidebar() {
    // Kita tetap ambil auth buat NavUser di bawah
    const { auth } = usePage<any>().props;

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={'#'}>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {/* Dashboard Utama */}
                <NavMain items={mainNavItems} label="Platform" />

                {/* System Management - Munculkan saja semua dulu */}
                <NavMain
                    items={usersNavItems}
                    label="System Management"
                />

                {/* Tata Tertib - Pasal */}
                <NavMain
                    items={tatibNavItems}
                    label="Tata Tertib"
                />

                {/* Data Siswa */}
                <NavMain
                    items={dataNavItems}
                    label="Data Sekolah"
                />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
// }
