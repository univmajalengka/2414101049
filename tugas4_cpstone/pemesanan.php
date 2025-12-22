<?php
$paket = isset($_GET['paket']) ? $_GET['paket'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Pemesanan | Curug Cinulang</title>
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
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="font-bold text-lg text-primary">Curug Cinulang</div>
      <div class="flex items-center gap-4 text-sm font-medium">
        <a href="index.php" class="hover:text-primary">Home</a>
        <a href="pemesanan.php" class="hover:text-primary">Pesan Paket</a>
        <a href="modifikasi_pesanan.php" class="hover:text-primary">Daftar Pesanan</a>
      </div>
    </div>
  </nav>

  <main class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 p-6 md:p-8">
      <h1 class="text-2xl font-bold">Form Pemesanan Paket Wisata</h1>
      <p class="text-sm text-slate-500 dark:text-slate-300 mt-2">Paket terpilih: <span class="font-semibold text-primary"><?php echo $paket ? htmlspecialchars($paket) : 'Semua Paket'; ?></span></p>

      <?php if ($error): ?>
        <div class="mt-4 px-4 py-3 rounded-xl bg-rose-100 text-rose-700 border border-rose-200">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="proses_simpan.php" method="POST" class="mt-6 space-y-4" id="formPesan">
        <div>
          <label class="text-sm font-medium">Nama Pemesan</label>
          <input type="text" name="nama_pemesan" class="mt-1 w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary dark:bg-slate-950 dark:border-slate-700" placeholder="Radit">
        </div>
        <div>
          <label class="text-sm font-medium">Nomor HP / Telepon</label>
          <input type="text" name="no_hp" class="mt-1 w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary dark:bg-slate-950 dark:border-slate-700" placeholder="08xxxxxxxxxx">
        </div>
        <div>
          <label class="text-sm font-medium">Tanggal Pesan</label>
          <input type="date" name="tanggal_pesan" class="mt-1 w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary dark:bg-slate-950 dark:border-slate-700">
        </div>
        <div>
          <label class="text-sm font-medium">Waktu Pelaksanaan Perjalanan (Jumlah Hari)</label>
          <input type="number" name="hari" min="1" class="mt-1 w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary dark:bg-slate-950 dark:border-slate-700" placeholder="Contoh: 2">
        </div>
        <div>
          <label class="text-sm font-medium">Pelayanan</label>
          <div class="mt-2 grid gap-2">
            <label class="flex items-center gap-2">
              <input type="checkbox" name="layanan[]" value="Penginapan" data-price="1000000" class="rounded border-slate-300 text-primary focus:ring-primary">
              <span>Penginapan (Rp 1.000.000)</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" name="layanan[]" value="Transportasi" data-price="1200000" class="rounded border-slate-300 text-primary focus:ring-primary">
              <span>Transportasi (Rp 1.200.000)</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" name="layanan[]" value="Service / Makan" data-price="500000" class="rounded border-slate-300 text-primary focus:ring-primary">
              <span>Service / Makan (Rp 500.000)</span>
            </label>
          </div>
        </div>
        <div>
          <label class="text-sm font-medium">Jumlah Peserta</label>
          <input type="number" name="jumlah_peserta" min="1" class="mt-1 w-full rounded-xl border-slate-200 focus:border-primary focus:ring-primary dark:bg-slate-950 dark:border-slate-700" placeholder="Contoh: 5">
        </div>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-medium">Harga Paket Perjalanan</label>
            <input type="text" name="harga_paket" id="harga_paket" readonly class="mt-1 w-full rounded-xl border-slate-200 bg-slate-100 dark:bg-slate-800 dark:border-slate-700">
          </div>
          <div>
            <label class="text-sm font-medium">Jumlah Tagihan</label>
            <input type="text" name="jumlah_tagihan" id="jumlah_tagihan" readonly class="mt-1 w-full rounded-xl border-slate-200 bg-slate-100 dark:bg-slate-800 dark:border-slate-700">
          </div>
        </div>

        <button type="submit" class="w-full mt-2 px-4 py-3 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700">Kirim Pemesanan</button>
      </form>
    </div>
  </main>

  <script>
    const form = document.getElementById('formPesan');
    const hargaPaketEl = document.getElementById('harga_paket');
    const jumlahTagihanEl = document.getElementById('jumlah_tagihan');

    const formatRupiah = (angka) => {
      return new Intl.NumberFormat('id-ID').format(angka);
    };

    const hitung = () => {
      const layanan = Array.from(document.querySelectorAll('input[name="layanan[]"]:checked'));
      const hargaPaket = layanan.reduce((sum, item) => sum + parseInt(item.dataset.price || '0', 10), 0);
      const hari = parseInt(document.querySelector('input[name="hari"]').value || '0', 10);
      const peserta = parseInt(document.querySelector('input[name="jumlah_peserta"]').value || '0', 10);
      const jumlahTagihan = hari * peserta * hargaPaket;

      hargaPaketEl.value = hargaPaket ? `Rp ${formatRupiah(hargaPaket)}` : '';
      jumlahTagihanEl.value = jumlahTagihan ? `Rp ${formatRupiah(jumlahTagihan)}` : '';
    };

    document.querySelectorAll('input').forEach((el) => {
      el.addEventListener('input', hitung);
      el.addEventListener('change', hitung);
    });

    form.addEventListener('submit', (e) => {
      const required = ['nama_pemesan', 'no_hp', 'tanggal_pesan', 'hari', 'jumlah_peserta'];
      let valid = true;
      required.forEach((name) => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field.value.trim()) {
          valid = false;
          field.classList.add('border-rose-400');
        } else {
          field.classList.remove('border-rose-400');
        }
      });

      const layananChecked = form.querySelectorAll('input[name="layanan[]"]:checked').length;
      if (layananChecked === 0) {
        valid = false;
        alert('Pilih minimal satu layanan.');
      }

      if (!valid) {
        e.preventDefault();
        alert('Lengkapi semua field yang wajib diisi.');
      }
    });
  </script>
</body>
</html>
