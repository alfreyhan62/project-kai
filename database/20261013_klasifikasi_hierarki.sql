ALTER TABLE klasifikasi_arsip
    MODIFY kode_klasifikasi VARCHAR(50) NULL,
    ADD COLUMN parent_id INT UNSIGNED NULL AFTER klasifikasi_id,
    ADD COLUMN tingkat TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER parent_id,
    ADD COLUMN urutan INT UNSIGNED NOT NULL DEFAULT 0 AFTER tingkat,
    ADD COLUMN sumber_kunci VARCHAR(100) NULL AFTER urutan,
    ADD KEY idx_klasifikasi_parent_urutan (parent_id, urutan),
    ADD UNIQUE KEY uq_klasifikasi_sumber_kunci (sumber_kunci),
    ADD CONSTRAINT fk_klasifikasi_parent FOREIGN KEY (parent_id) REFERENCES klasifikasi_arsip(klasifikasi_id) ON DELETE RESTRICT ON UPDATE CASCADE;
