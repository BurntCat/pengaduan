	-- =========================================================
	-- Database: db_pengaduan
	-- Aplikasi Form Pengaduan (CodeIgniter 4)
	-- Jalankan file ini di SQLyog / MySQL Workbench / phpMyAdmin
	-- =========================================================

	CREATE DATABASE IF NOT EXISTS `db_pengaduan`
	  DEFAULT CHARACTER SET utf8mb4
	  DEFAULT COLLATE utf8mb4_unicode_ci;

	USE `db_pengaduan`;

	-- ---------------------------------------------------------
	-- 1. Tabel master kategori pengaduan
	-- ---------------------------------------------------------
	CREATE TABLE `kategori_pengaduan` (
	  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `nama_kategori`  VARCHAR(100) NOT NULL,
	  `is_active`      TINYINT(1) NOT NULL DEFAULT 1,
	  `created_at`     DATETIME NULL,
	  `updated_at`     DATETIME NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB;

	INSERT INTO `kategori_pengaduan` (`nama_kategori`, `created_at`) VALUES
	('Pelayanan Publik', NOW()),
	('Infrastruktur & Fasilitas Umum', NOW()),
	('Dugaan Korupsi / Pungli', NOW()),
	('Kepegawaian / ASN', NOW()),
	('Lingkungan Hidup', NOW()),
	('Lainnya', NOW());

	-- ---------------------------------------------------------
	-- 2. Tabel master lokasi kejadian
	-- ---------------------------------------------------------
	CREATE TABLE `lokasi_kejadian` (
	  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `nama_lokasi`  VARCHAR(150) NOT NULL,
	  `kode_wilayah` VARCHAR(20)  NULL,
	  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
	  `created_at`   DATETIME NULL,
	  `updated_at`   DATETIME NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB;

	INSERT INTO `lokasi_kejadian` (`nama_lokasi`, `kode_wilayah`, `created_at`) VALUES
	('Kota Surabaya', 'JATIM', NOW()),
	('Kabupaten Sidoarjo', 'JATIM', NOW()),
	('Kabupaten Gresik', 'JATIM', NOW()),
	('Kota Malang', 'JATIM', NOW()),
	('Kabupaten Mojokerto', 'JATIM', NOW()),
	('Kabupaten Lamongan', 'JATIM', NOW());

	-- ---------------------------------------------------------
	-- 3. Tabel tahapan default & SLA default per kategori
	--    (dipakai admin untuk mengatur SLA default tiap tahap)
	-- ---------------------------------------------------------
	CREATE TABLE `sla_default` (
	  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `kategori_id`   INT UNSIGNED NOT NULL,
	  `tahap`         ENUM('diterima','diverifikasi','ditangani_bidang','selesai') NOT NULL,
	  `sla_hari`      INT UNSIGNED NOT NULL DEFAULT 1,
	  `urutan`        TINYINT UNSIGNED NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `uq_kategori_tahap` (`kategori_id`, `tahap`),
	  CONSTRAINT `fk_sla_default_kategori`
	    FOREIGN KEY (`kategori_id`) REFERENCES `kategori_pengaduan` (`id`)
	    ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB;

	-- SLA default untuk semua kategori (bisa diubah admin lewat halaman admin)
	INSERT INTO `sla_default` (`kategori_id`, `tahap`, `sla_hari`, `urutan`)
	SELECT id, 'diterima', 1, 1 FROM `kategori_pengaduan`;
	INSERT INTO `sla_default` (`kategori_id`, `tahap`, `sla_hari`, `urutan`)
	SELECT id, 'diverifikasi', 1, 2 FROM `kategori_pengaduan`;
	INSERT INTO `sla_default` (`kategori_id`, `tahap`, `sla_hari`, `urutan`)
	SELECT id, 'ditangani_bidang', 5, 3 FROM `kategori_pengaduan`;
	INSERT INTO `sla_default` (`kategori_id`, `tahap`, `sla_hari`, `urutan`)
	SELECT id, 'selesai', 3, 4 FROM `kategori_pengaduan`;

	-- ---------------------------------------------------------
	-- 4. Tabel utama pengaduan
	-- ---------------------------------------------------------
	CREATE TABLE `pengaduan` (
	  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `nomor_tiket`         VARCHAR(30) NOT NULL,
	  `kategori_id`         INT UNSIGNED NOT NULL,
	  `lokasi_id`           INT UNSIGNED NOT NULL,
	  `kronologi`           TEXT NOT NULL,
	  `nama_pelapor`        VARCHAR(150) NOT NULL,
	  `email_pelapor`       VARCHAR(150) NULL,
	  `no_hp_pelapor`       VARCHAR(20)  NULL,
	  `rahasiakan_identitas` TINYINT(1) NOT NULL DEFAULT 1,
	  `status_akhir`        ENUM('diterima','diverifikasi','ditangani_bidang','selesai','ditolak') NOT NULL DEFAULT 'diterima',
	  `alasan_penolakan`    TEXT NULL,
	  `created_at`          DATETIME NOT NULL,
	  `updated_at`          DATETIME NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `uq_nomor_tiket` (`nomor_tiket`),
	  KEY `idx_kategori` (`kategori_id`),
	  KEY `idx_lokasi` (`lokasi_id`),
	  CONSTRAINT `fk_pengaduan_kategori`
	    FOREIGN KEY (`kategori_id`) REFERENCES `kategori_pengaduan` (`id`),
	  CONSTRAINT `fk_pengaduan_lokasi`
	    FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi_kejadian` (`id`)
	) ENGINE=INNODB;

	-- ---------------------------------------------------------
	-- 5. Tabel lampiran bukti (bisa lebih dari satu file)
	-- ---------------------------------------------------------
	CREATE TABLE `pengaduan_lampiran` (
	  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `pengaduan_id` INT UNSIGNED NOT NULL,
	  `nama_file`    VARCHAR(255) NOT NULL,
	  `path_file`    VARCHAR(255) NOT NULL,
	  `mime_type`    VARCHAR(100) NULL,
	  `ukuran_kb`    INT UNSIGNED NULL,
	  `created_at`   DATETIME NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `idx_pengaduan` (`pengaduan_id`),
	  CONSTRAINT `fk_lampiran_pengaduan`
	    FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`)
	    ON DELETE CASCADE
	) ENGINE=InnoDB;

	-- ---------------------------------------------------------
	-- 6. Tabel tahapan / progres tiap pengaduan (untuk timeline & SLA)
	-- ---------------------------------------------------------
	CREATE TABLE `pengaduan_tahapan` (
	  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `pengaduan_id`   INT UNSIGNED NOT NULL,
	  `tahap`          ENUM('diterima','diverifikasi','ditangani_bidang','selesai') NOT NULL,
	  `urutan`         TINYINT UNSIGNED NOT NULL,
	  `status`         ENUM('menunggu','proses','selesai') NOT NULL DEFAULT 'menunggu',
	  `sla_hari`       INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'diisi/diubah oleh admin',
	  `tanggal_mulai`  DATETIME NULL,
	  `tanggal_selesai` DATETIME NULL,
	  `keterangan`     VARCHAR(255) NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `uq_pengaduan_tahap` (`pengaduan_id`, `tahap`),
	  CONSTRAINT `fk_tahapan_pengaduan`
	    FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`)
	    ON DELETE CASCADE
	) ENGINE=InnoDB;

	-- ---------------------------------------------------------
	-- 7. Tabel audit trail (log seluruh aktivitas, sesuai kartu "Audit Trail")
	-- ---------------------------------------------------------
	CREATE TABLE `pengaduan_audit_trail` (
	  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `pengaduan_id` INT UNSIGNED NOT NULL,
	  `aktivitas`    VARCHAR(255) NOT NULL,
	  `oleh`         VARCHAR(100) NOT NULL DEFAULT 'Sistem',
	  `created_at`   DATETIME NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `idx_pengaduan` (`pengaduan_id`),
	  CONSTRAINT `fk_audit_pengaduan`
	    FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`)
	    ON DELETE CASCADE
	) ENGINE=InnoDB;

	-- ---------------------------------------------------------
	-- 8. (Opsional) Tabel admin sederhana untuk login halaman admin SLA
	-- ---------------------------------------------------------
	CREATE TABLE `admin` (
	  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `username`   VARCHAR(50) NOT NULL,
	  `password`   VARCHAR(255) NOT NULL COMMENT 'disimpan dengan password_hash()',
	  `nama`       VARCHAR(100) NOT NULL,
	  `created_at` DATETIME NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `uq_username` (`username`)
	) ENGINE=InnoDB;

	-- password default: admin123 (ganti setelah login pertama)
	INSERT INTO `admin` (`username`, `password`, `nama`, `created_at`) VALUES
	('admin', '$2y$10$V6l6qk9Z1s0m2u1oQ6r1UOe9m4l1o8Yy8v0z1b2W3o4q5s6t7u8v9', 'Administrator', NOW());
