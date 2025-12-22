<?php
require 'koneksi.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nama_pemesan = isset($_POST['nama_pemesan']) ? trim($_POST['nama_pemesan']) : '';
$no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
$tanggal_pesan = isset($_POST['tanggal_pesan']) ? trim($_POST['tanggal_pesan']) : '';
$hari = isset($_POST['hari']) ? (int)$_POST['hari'] : 0;
$jumlah_peserta = isset($_POST['jumlah_peserta']) ? (int)$_POST['jumlah_peserta'] : 0;
$layanan = isset($_POST['layanan']) ? $_POST['layanan'] : [];

if ($id <= 0 || $nama_pemesan === '' || $no_hp === '' || $tanggal_pesan === '' || $hari <= 0 || $jumlah_peserta <= 0 || count($layanan) === 0) {
    header('Location: modifikasi_pesanan.php?status=error');
    exit;
}

$hargaMap = [
    'Penginapan' => 1000000,
    'Transportasi' => 1200000,
    'Service / Makan' => 500000,
];

$totalHarga = 0;
$layananTerpilih = [];
foreach ($layanan as $item) {
    if (isset($hargaMap[$item])) {
        $totalHarga += $hargaMap[$item];
        $layananTerpilih[] = $item;
    }
}

if ($totalHarga <= 0) {
    header('Location: modifikasi_pesanan.php?status=error');
    exit;
}

$layananStr = implode(',', $layananTerpilih);
$jumlahTagihan = $hari * $jumlah_peserta * $totalHarga;

$stmt = $koneksi->prepare('UPDATE pesanan SET nama_pemesan = ?, no_hp = ?, tanggal_pesan = ?, hari = ?, jumlah_peserta = ?, layanan = ?, harga_paket = ?, jumlah_tagihan = ? WHERE id = ?');
$stmt->bind_param('sssiiisii', $nama_pemesan, $no_hp, $tanggal_pesan, $hari, $jumlah_peserta, $layananStr, $totalHarga, $jumlahTagihan, $id);

if ($stmt->execute()) {
    header('Location: modifikasi_pesanan.php?status=success_update');
    exit;
}

header('Location: modifikasi_pesanan.php?status=error');
exit;
?>
