export interface TahunAjaran {
    id: number;
    nama: string;
    slug: string;
    tgl_mulai: string;
    tgl_selesai: string;
    aktif: boolean;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
