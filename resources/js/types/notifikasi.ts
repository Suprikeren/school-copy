export interface Notifikasi {
    id: number;
    user_id: number;
    tipe: string;
    isi: string;
    tautan: string | null;
    dibaca_pada: string | null;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
