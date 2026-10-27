export interface JenisPelanggaran {
    id: number;
    nama: string;
    kategori: string;
    poin: number;
    aktif: boolean;
    slug: string;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
