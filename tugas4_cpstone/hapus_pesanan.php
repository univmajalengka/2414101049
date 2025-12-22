<?php
require 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: modifikasi_pesanan.php');
    exit;
}

$stmt = $koneksi->prepare('DELETE FROM pesanan WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    header('Location: modifikasi_pesanan.php?status=success_delete');
    exit;
}

header('Location: modifikasi_pesanan.php?status=error');
exit;
?>
