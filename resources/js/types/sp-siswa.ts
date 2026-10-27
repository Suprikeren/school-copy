export interface SpSiswa {
    id: number;
    siswa_id: number;
    tahun_ajaran_id: number;
    level: number;
    poin_saat_tercapai: number;
    tanggal: string;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
