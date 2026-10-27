export interface Kasus {
    id: number;
    tahun_ajaran_id: number;
    waktu: string;
    pencatat_id: number;
    keterangan: string | null;
    foto_path: string | null;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
