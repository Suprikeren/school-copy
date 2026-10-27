export interface RiwayatKelas {
    id: number;
    siswa_id: number;
    tahun_ajaran_id: number;
    kelas: string;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
