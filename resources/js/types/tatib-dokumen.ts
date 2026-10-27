export interface TatibDokumen {
    id: number;
    nama_file: string;
    path: string;
    diunggah_oleh: number;
    tanggal: string;
    created_by: string | null;
    updated_by: string | null;
    created_at: string;
    updated_at: string;
}
