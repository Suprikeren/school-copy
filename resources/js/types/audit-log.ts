export interface AuditLog {
    id: number;
    user_id: number | null;
    aksi: string;
    tabel: string;
    data_id: number | null;
    data_lama: Record<string, unknown> | null;
    data_baru: Record<string, unknown> | null;
    ip: string | null;
    waktu: string;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
