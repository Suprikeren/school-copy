export interface TindakLanjut {
    id: number;
    siswa_id: number;
    sp_siswa_id: number | null;
    pelanggaran_id: number | null;
    tindakan: string;
    tanggal: string;
    catatan: string | null;
    status: string;
    ditangani_oleh: number;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
