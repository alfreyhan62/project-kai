ALTER TABLE arsip
    ADD COLUMN nomor_spk VARCHAR(100) NULL,
    ADD COLUMN tanggal_spk DATE NULL,
    ADD COLUMN vendor VARCHAR(255) NULL,
    ADD COLUMN nama_pekerjaan VARCHAR(500) NULL,
    ADD COLUMN nilai_spk DECIMAL(18,2) NULL,
    ADD COLUMN kategori_nilai VARCHAR(30) NULL,
    ADD COLUMN tanggal_mulai_pekerjaan DATE NULL,
    ADD COLUMN tanggal_selesai_pekerjaan DATE NULL,
    ADD COLUMN jangka_waktu INT UNSIGNED NULL,
    ADD COLUMN penanggung_jawab_unit_id INT UNSIGNED NULL,
    ADD COLUMN status_spk VARCHAR(20) NULL;
