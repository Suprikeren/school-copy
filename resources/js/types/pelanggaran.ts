export interface Pelanggaran {
    id: number;
    kasus_id: number;
    siswa_id: number;
    tahun_ajaran_id: number;
    jenis_pelanggaran_id: number;
    nama_pelanggaran: string;
    poin: number;
    kelas: string;
    pasal: string;
    deleted_at: string | null;
    alasan_hapus: string | null;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
