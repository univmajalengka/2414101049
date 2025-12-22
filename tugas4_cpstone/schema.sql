CREATE DATABASE IF NOT EXISTS capstone_curug_cinulang;
USE capstone_curug_cinulang;

CREATE TABLE IF NOT EXISTS pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pemesan VARCHAR(100) NOT NULL,
  no_hp VARCHAR(30) NOT NULL,
  tanggal_pesan DATE NOT NULL,
  hari INT NOT NULL,
  jumlah_peserta INT NOT NULL,
  layanan VARCHAR(255) NOT NULL,
  harga_paket INT NOT NULL,
  jumlah_tagihan BIGINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
