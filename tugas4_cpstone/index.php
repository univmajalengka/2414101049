<?php
$paketList = [
    [
        'id' => 'basic',
        'nama' => 'Paket Jelajah Curug',
        'deskripsi' => 'Eksplorasi spot utama Curug Cinulang dengan pemandu lokal dan area foto terbaik.',
        'img' => 'assets/paket1.svg',
        'youtube' => 'https://www.youtube.com/watch?v=VfPN0F3k6Go'
    ],
    [
        'id' => 'adventure',
        'nama' => 'Paket Petualangan',
        'deskripsi' => 'Rute trekking ringan, sunrise view, dan dokumentasi singkat untuk konten perjalanan.',
        'img' => 'assets/paket2.svg',
        'youtube' => 'https://www.youtube.com/watch?v=3_lOCG_TG98'
    ],
    [
        'id' => 'family',
        'nama' => 'Paket Keluarga',
        'deskripsi' => 'Cocok untuk keluarga dengan fasilitas nyaman, area istirahat, dan itinerary santai.',
        'img' => 'assets/paket3.svg',
        'youtube' => 'https://www.youtube.com/watch?v=5B6U2ZxZ6FM'
    ],
];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Curug Cinulang Sumedang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#2563eb'
          }
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
        <button id="toggleTheme" class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800">Mode</button>
      </div>
    </div>
  </nav>

  <header class="max-w-6xl mx-auto px-4 py-14">
    <div class="grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white">Curug Cinulang Sumedang</h1>
        <p class="mt-4 text-slate-600 dark:text-slate-300">Wisata alam air terjun yang jadi destinasi unggulan Sumedang. Curug Cinulang menawarkan panorama hijau, udara segar, dan spot foto epik yang cocok untuk semua kalangan.</p>
        <div class="mt-6 flex gap-3">
          <a href="pemesanan.php" class="px-5 py-3 rounded-xl bg-primary text-white shadow-lg shadow-blue-200/50 hover:bg-blue-700">Pesan Sekarang</a>
          <a href="modifikasi_pesanan.php" class="px-5 py-3 rounded-xl border border-slate-300 dark:border-slate-700">Lihat Pesanan</a>
        </div>
      </div>
      <div class="relative">
        <div class="absolute -inset-6 bg-gradient-to-br from-blue-200 via-blue-100 to-emerald-100 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-2xl blur-2xl"></div>
        <img src="assets/paket1.svg" alt="Curug Cinulang" class="relative rounded-2xl shadow-xl w-full">
      </div>
    </div>
  </header>

  <section class="max-w-6xl mx-auto px-4 pb-16">
    <div class="flex items-end justify-between mb-6">
      <h2 class="text-2xl font-bold">Paket Wisata Populer</h2>
      <p class="text-sm text-slate-500">Pilih pengalaman terbaik untuk perjalananmu</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      <?php foreach ($paketList as $paket): ?>
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg overflow-hidden border border-slate-100 dark:border-slate-800">
          <img src="<?php echo htmlspecialchars($paket['img']); ?>" alt="<?php echo htmlspecialchars($paket['nama']); ?>" class="w-full h-44 object-cover">
          <div class="p-5">
            <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($paket['nama']); ?></h3>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-2"><?php echo htmlspecialchars($paket['deskripsi']); ?></p>
            <div class="mt-4 flex flex-col gap-2">
              <a href="<?php echo htmlspecialchars($paket['youtube']); ?>" target="_blank" class="w-full text-center px-4 py-2 rounded-xl bg-emerald-500 text-white hover:bg-emerald-600">Video Promosi</a>
              <a href="pemesanan.php?paket=<?php echo urlencode($paket['id']); ?>" class="w-full text-center px-4 py-2 rounded-xl bg-primary text-white hover:bg-blue-700">Pesan Sekarang</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer class="border-t border-slate-200 dark:border-slate-800 py-6">
    <div class="max-w-6xl mx-auto px-4 text-sm text-slate-500">© 2025 Curug Cinulang Sumedang. Dikelola oleh UMKM lokal.</div>
  </footer>

  <script>
    const toggleTheme = document.getElementById('toggleTheme');
    toggleTheme.addEventListener('click', () => {
      document.documentElement.classList.toggle('dark');
    });
  </script>
</body>
</html>
