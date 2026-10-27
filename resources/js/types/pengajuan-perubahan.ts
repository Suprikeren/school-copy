export interface PengajuanPerubahan {
    id: number;
    pelanggaran_id: number;
    diajukan_oleh: number;
    alasan: string;
    data_lama: Record<string, unknown>;
    data_baru: Record<string, unknown>;
    status: string;
    diputuskan_oleh: number | null;
    catatan_keputusan: string | null;
    waktu: string;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
