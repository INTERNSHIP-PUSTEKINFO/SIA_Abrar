  -- --------------------------------------------------------
  -- Host:                         127.0.0.1
  -- Server version:               8.0.30 - MySQL Community Server - GPL
  -- Server OS:                    Win64
  -- HeidiSQL Version:             12.1.0.6537
  -- --------------------------------------------------------

  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET NAMES utf8 */;
  /*!50503 SET NAMES utf8mb4 */;
  /*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
  /*!40103 SET TIME_ZONE='+00:00' */;
  /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
  /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
  /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


  -- Dumping database structure for db_akademik
  DROP DATABASE IF EXISTS `db_akademik`;
  CREATE DATABASE IF NOT EXISTS `db_akademik` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
  USE `db_akademik`;

  -- Dumping structure for table db_akademik.absensi
  DROP TABLE IF EXISTS `absensi`;
  CREATE TABLE IF NOT EXISTS `absensi` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `siswa_nis` varchar(20) NOT NULL,
    `mapel_id` bigint DEFAULT NULL,
    `tanggal` date DEFAULT NULL,
    `keterangan` enum('Hadir','Izin', 'Sakit') DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `FKnis_absen` (`siswa_nis`),
    KEY `FKmapel_absen` (`mapel_id`),
    CONSTRAINT `FKmapel_absen` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKnis_absen` FOREIGN KEY (`siswa_nis`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.guru
  DROP TABLE IF EXISTS `guru`;
  CREATE TABLE IF NOT EXISTS `guru` (
    `nip` varchar(20) NOT NULL,
    `user_id` bigint NOT NULL,
    `tempat_lahir` varchar(50) NOT NULL,
    `tanggal_lahir` date NOT NULL,
    `jenis_kelamin` enum('L','P') NOT NULL,
    `alamat` text NOT NULL,
    `mapel_id` bigint NOT NULL DEFAULT '0',
    PRIMARY KEY (`nip`),
    KEY `FKuser_guru` (`user_id`),
    KEY `FKmapel_guru` (`mapel_id`),
    CONSTRAINT `FKmapel_guru` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKuser_guru` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.jadwal
  DROP TABLE IF EXISTS `jadwal`;
  CREATE TABLE IF NOT EXISTS `jadwal` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `kelas_id` bigint DEFAULT NULL,
    `mapel_id` bigint DEFAULT NULL,
    `guru_id` bigint DEFAULT NULL,
    `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
    `jam_ke` bigint NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `FKkelas_jadwal` (`kelas_id`),
    KEY `FKmapel_jadwal` (`mapel_id`),
    KEY `FKguru_jadwal` (`guru_id`),
    CONSTRAINT `FKguru_jadwal` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKkelas_jadwal` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKmapel_jadwal` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.jurusan
  DROP TABLE IF EXISTS `jurusan`;
  CREATE TABLE IF NOT EXISTS `jurusan` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `kode_jurusan` varchar(10) NOT NULL,
    `nama_mapel` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.kelas
  DROP TABLE IF EXISTS `kelas`;
  CREATE TABLE IF NOT EXISTS `kelas` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `nama_kelas` varchar(10) DEFAULT NULL,
    `tingkat` enum('X','XI','XII') DEFAULT NULL,
    `jurusan_id` bigint NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `FKjurusan_kelas` (`jurusan_id`),
    CONSTRAINT `FKjurusan_kelas` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.mapel
  DROP TABLE IF EXISTS `mapel`;
  CREATE TABLE IF NOT EXISTS `mapel` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `kode_mapel` varchar(10) NOT NULL,
    `nama_mapel` varchar(100) NOT NULL,
    `jurusan_id` bigint NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `FKjurusan_id` (`jurusan_id`),
    CONSTRAINT `FKjurusan_id` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.nilai
  DROP TABLE IF EXISTS `nilai`;
  CREATE TABLE IF NOT EXISTS `nilai` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `siswa_nis` varchar(20) NOT NULL,
    `mapel_id` bigint NOT NULL DEFAULT '0',
    `semester` enum('Ganjil','Genap') NOT NULL,
    `tahun_ajaran` varchar(9) NOT NULL DEFAULT '0',
    `nilai_tugas` float NOT NULL DEFAULT '0',
    `nilai_uts` float NOT NULL DEFAULT '0',
    `nilai_uas` float NOT NULL DEFAULT '0',
    `nilai_akhir` float NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `FKsiswa_nis` (`siswa_nis`),
    KEY `FKmapel_nilai` (`mapel_id`),
    CONSTRAINT `FKmapel_nilai` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKsiswa_nis` FOREIGN KEY (`siswa_nis`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.siswa
  DROP TABLE IF EXISTS `siswa`;
  CREATE TABLE IF NOT EXISTS `siswa` (
    `nis` varchar(20) NOT NULL,
    `user_id` bigint DEFAULT NULL,
    `tempat_lahir` varchar(50) DEFAULT NULL,
    `tanggal_lahir` date DEFAULT NULL,
    `jenis_kelamin` enum('L','P') DEFAULT NULL,
    `alamat` text,
    `jurusan_id` bigint DEFAULT NULL,
    `kelas_id` bigint DEFAULT NULL,
    `tahun_masuk` year DEFAULT NULL,
    `status` enum('aktif','lulus','keluar') DEFAULT NULL,
    PRIMARY KEY (`nis`),
    KEY `FKuser_siswa` (`user_id`),
    KEY `FKjurusan_siswa` (`jurusan_id`),
    KEY `FKkelas_siswa` (`kelas_id`),
    CONSTRAINT `FKjurusan_siswa` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKkelas_siswa` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FKuser_siswa` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  -- Dumping structure for table db_akademik.users
  DROP TABLE IF EXISTS `users`;
  CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
    `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    `role` enum('admin','guru','siswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

  -- Data exporting was unselected.

  /*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
  /*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
  /*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
