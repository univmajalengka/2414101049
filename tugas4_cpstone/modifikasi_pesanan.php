<?php
require 'koneksi.php';

$status = isset($_GET['status']) ? $_GET['status'] : '';

$stmt = $koneksi->prepare('SELECT id, nama_pemesan, no_hp, tanggal_pesan, hari, jumlah_peserta, layanan, harga_paket, jumlah_tagihan FROM pesanan ORDER BY id DESC');
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pesanan | Curug Cinulang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: '#2563eb' }
        }
      }
    }
  </script>
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100">
  <nav class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200 dark:bg-slate-950/90 dark:border-slate-800">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="font-bold text-lg text-primary">Curug Cinulang</div>
      <div class="flex items-center gap-4 text-sm font-medium">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="pemesanan.php" class="hover:text-primary">Pesan Paket</a>
        <a href="modifikasi_pesanan.php" class="hover:text-primary">Daftar Pesanan</a>
      </div>
    </div>
  </nav>

  <main class="max-w-6xl mx-auto px-4 py-10">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Modifikasi Pesanan</h1>
        <a href="pemesanan.php" class="px-4 py-2 rounded-xl bg-primary text-white">Tambah Pesanan</a>
      </div>

      <?php if ($status === 'success_simpan'): ?>
        <div class="mt-4 px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700 border border-emerald-200">Pemesanan berhasil disimpan.</div>
      <?php elseif ($status === 'success_update'): ?>
        <div class="mt-4 px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700 border border-emerald-200">Pemesanan berhasil diperbarui.</div>
      <?php elseif ($status === 'success_delete'): ?>
        <div class="mt-4 px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700 border border-emerald-200">Pemesanan berhasil dihapus.</div>
      <?php endif; ?>

      <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left bg-slate-100 dark:bg-slate-800">
              <th class="p-3">Nama Pemesan</th>
              <th class="p-3">No HP</th>
              <th class="p-3">Tanggal</th>
              <th class="p-3">Hari</th>
              <th class="p-3">Peserta</th>
              <th class="p-3">Layanan</th>
              <th class="p-3">Harga Paket</th>
              <th class="p-3">Jumlah Tagihan</th>
              <th class="p-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b border-slate-200 dark:border-slate-800">
                <td class="p-3"><?php echo htmlspecialchars($row['nama_pemesan']); ?></td>
                <td class="p-3"><?php echo htmlspecialchars($row['no_hp']); ?></td>
                <td class="p-3"><?php echo htmlspecialchars($row['tanggal_pesan']); ?></td>
                <td class="p-3"><?php echo (int)$row['hari']; ?></td>
                <td class="p-3"><?php echo (int)$row['jumlah_peserta']; ?></td>
                <td class="p-3"><?php echo htmlspecialchars($row['layanan']); ?></td>
                <td class="p-3">Rp <?php echo number_format($row['harga_paket'], 0, ',', '.'); ?></td>
                <td class="p-3">Rp <?php echo number_format($row['jumlah_tagihan'], 0, ',', '.'); ?></td>
                <td class="p-3 flex gap-2">
                  <a href="edit_pesanan.php?id=<?php echo $row['id']; ?>" class="px-3 py-1 rounded-lg bg-amber-400 text-white">Edit</a>
                  <a href="hapus_pesanan.php?id=<?php echo $row['id']; ?>" class="px-3 py-1 rounded-lg bg-rose-500 text-white" onclick="return confirm('Hapus pesanan ini?');">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
