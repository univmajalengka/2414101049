<?php
include 'connection.php';

$produk = [];
$dbError = '';

$sql = "SELECT id, nama, harga, deskripsi, gambar, created_at FROM produk ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result instanceof mysqli_result) {
  while ($row = $result->fetch_assoc()) {
    $produk[] = $row;
  }
  $result->free();
} else {
  $dbError = $conn->error;
}

$conn->close();

function formatRupiah($value)
{
  if (!is_numeric($value)) {
    return 'Rp 0';
  }
  return 'Rp ' . number_format((float) $value, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <title>Warung Putra Barokah - Hangatnya Sampe Hati</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              milk: "#FAF9F6",
              "orange-deep": "#FF6A00",
            },
            fontFamily: {
              display: ['"Plus Jakarta Sans"', "ui-sans-serif", "system-ui"],
              body: ['"Plus Jakarta Sans"', "ui-sans-serif", "system-ui"],
            },
          },
        },
      };
    </script>
  </head>
  <body class="bg-milk font-body">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex-shrink-0">
            <h1 class="text-2xl font-bold text-orange-deep">
              Warung Putra Barokah
            </h1>
          </div>

          <!-- Desktop Menu -->
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-8">
              <a
                href="#home"
                class="text-gray-700 hover:text-orange-deep px-3 py-2 text-sm font-medium transition-colors"
                >Beranda</a
              >
              <a
                href="#menu"
                class="text-gray-700 hover:text-orange-deep px-3 py-2 text-sm font-medium transition-colors"
                >Menu</a
              >
              <a
                href="#testimonials"
                class="text-gray-700 hover:text-orange-deep px-3 py-2 text-sm font-medium transition-colors"
                >Testimoni</a
              >
              <a
                href="#contact"
                class="text-gray-700 hover:text-orange-deep px-3 py-2 text-sm font-medium transition-colors"
                >Kontak</a
              >
              <a
                href="admin/login.php"
                class="text-gray-700 hover:text-orange-deep px-3 py-2 text-sm font-medium transition-colors"
                >Login Admin</a
              >
            </div>
          </div>

          <!-- CTA Button -->
          <div class="hidden md:block">
            <a
              href="#menu"
              class="bg-orange-deep text-white px-6 py-2 rounded-full hover:bg-orange-600 transition-colors font-medium inline-block"
            >
              Pesan Sekarang
            </a>
          </div>

          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button
              id="mobile-menu-btn"
              class="text-gray-700 hover:text-orange-deep focus:outline-none"
            >
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <a
            href="#home"
            class="block px-3 py-2 text-gray-700 hover:text-orange-deep"
            >Beranda</a
          >
          <a
            href="#menu"
            class="block px-3 py-2 text-gray-700 hover:text-orange-deep"
            >Menu</a
          >
          <a
            href="#testimonials"
            class="block px-3 py-2 text-gray-700 hover:text-orange-deep"
            >Testimoni</a
          >
          <a
            href="#contact"
            class="block px-3 py-2 text-gray-700 hover:text-orange-deep"
            >Kontak</a
          >
          <a
            href="admin/login.php"
            class="block px-3 py-2 text-gray-700 hover:text-orange-deep"
            >Login Admin</a
          >
          <a
            href="#menu"
            class="w-full mt-2 inline-block bg-orange-deep text-white px-6 py-2 rounded-full hover:bg-orange-600 transition-colors font-medium text-center"
          >
            Pesan Sekarang
          </a>
        </div>
      </div>
    </nav>

    <!-- Hero Carousel -->
    <section id="home" class="relative h-96 md:h-[500px] overflow-hidden">
      <div id="carousel" class="relative w-full h-full">
        <div
          class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-100"
         >
          <img
            src="menu/saya.jpg"
            alt="Bakso"
            class="w-full h-full object-cover"
          />
          <div
            class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
          >
            <div class="text-center text-white px-4">
              <h2 class="text-4xl md:text-6xl font-bold mb-4">
                Warung Putra Barokah
              </h2>
              <p class="text-xl md:text-2xl mb-8">Hangatnya Sampe Hati</p>
              <div class="space-x-4">
                <button
                  class="bg-orange-deep text-white px-8 py-3 rounded-full hover:bg-orange-600 transition-colors font-medium"
                >
                  Lihat Menu
                </button>
                <button
                  class="border-2 border-white text-white px-8 py-3 rounded-full hover:bg-white hover:text-gray-800 transition-colors font-medium"
                >
                  Testimoni
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- Slide 1 -->
        <div
          class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-100"
         >
          <img
            src="menu/baksotetelan.jpg"
            alt="Bakso"
            class="w-full h-full object-cover"
          />
          <div
            class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
          >
            <div class="text-center text-white px-4">
              <h2 class="text-4xl md:text-6xl font-bold mb-4">
                Warung Putra Barokah
              </h2>
              <p class="text-xl md:text-2xl mb-8">Hangatnya Sampe Hati</p>
              <div class="space-x-4">
                <button
                  class="bg-orange-deep text-white px-8 py-3 rounded-full hover:bg-orange-600 transition-colors font-medium"
                >
                  Lihat Menu
                </button>
                <button
                  class="border-2 border-white text-white px-8 py-3 rounded-full hover:bg-white hover:text-gray-800 transition-colors font-medium"
                >
                  Testimoni
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div
          class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-0"
        >
          <img
            src="menu/mieayam.jpg"
            alt="Mie Ayam"
            class="w-full h-full object-cover"
          />
          <div
            class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
          >
            <div class="text-center text-white px-4">
              <h2 class="text-4xl md:text-6xl font-bold mb-4">
                Mie Ayam Spesial
              </h2>
              <p class="text-xl md:text-2xl mb-8">Kelezatan Tiada Tara</p>
              <div class="space-x-4">
                <button
                  class="bg-orange-deep text-white px-8 py-3 rounded-full hover:bg-orange-600 transition-colors font-medium"
                >
                  Lihat Menu
                </button>
                <button
                  class="border-2 border-white text-white px-8 py-3 rounded-full hover:bg-white hover:text-gray-800 transition-colors font-medium"
                >
                  Testimoni
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 3 -->
        <div
          class="carousel-slide absolute inset-0 transition-opacity duration-500 opacity-0"
        >
          <img
            src="menu/esbuah.jpg"
            alt="Es Buah"
            class="w-full h-full object-cover"
          />
          <div
            class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center"
          >
            <div class="text-center text-white px-4">
              <h2 class="text-4xl md:text-6xl font-bold mb-4">Es Buah Segar</h2>
              <p class="text-xl md:text-2xl mb-8">Penyegar Dahaga</p>
              <div class="space-x-4">
                <button
                  class="bg-orange-deep text-white px-8 py-3 rounded-full hover:bg-orange-600 transition-colors font-medium"
                >
                  Lihat Menu
                </button>
                <button
                  class="border-2 border-white text-white px-8 py-3 rounded-full hover:bg-white hover:text-gray-800 transition-colors font-medium"
                >
                  Testimoni
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carousel Controls -->
      <button
        id="prev-btn"
        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-40 text-white p-2 rounded-full transition-all"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          ></path>
        </svg>
      </button>
      <button
        id="next-btn"
        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-40 text-white p-2 rounded-full transition-all"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5l7 7-7 7"
          ></path>
        </svg>
      </button>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Menu Pilihan
          </h2>
          <p class="text-gray-600 text-lg">
            Nikmati berbagai pilihan menu lezat kami
          </p>
        </div>

        <!-- Filter Buttons -->
          <div class="flex justify-center mb-8">
            <div class="bg-gray-100 p-1 rounded-full">
              <button
                class="filter-btn active px-6 py-2 rounded-full font-medium transition-colors"
                data-filter="all"
            >
              Semua
            </button>
            <button
              class="filter-btn px-6 py-2 rounded-full font-medium transition-colors"
              data-filter="makanan"
            >
              Makanan
            </button>
            <button
              class="filter-btn px-6 py-2 rounded-full font-medium transition-colors"
              data-filter="minuman"
            >
              Minuman
              </button>
            </div>
          </div>

          <?php if ($dbError): ?>
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-600">
              Terjadi kesalahan saat memuat menu: <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <!-- Menu Grid -->
          <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
          >
            <?php if (!empty($produk)): ?>
              <?php foreach ($produk as $item): ?>
                <?php
                  $imageFile = $item['gambar'] ?? '';
                  $namaProduk = $item['nama'] ?? '';
                  $deskripsiProduk = $item['deskripsi'] ?? '';

                  $imageSrc = !empty($imageFile)
                    ? 'menu/' . htmlspecialchars($imageFile, ENT_QUOTES, 'UTF-8')
                    : 'https://via.placeholder.com/400x320?text=Produk';

                  $kategori = 'makanan';
                  $namaLower = strtolower($namaProduk);
                  $deskripsiLower = strtolower($deskripsiProduk);
                  $drinkPatterns = [
                    "/\\bes\\b/",
                    "/\\bjus\\b/",
                    "/\\bteh\\b/",
                    "/\\bkopi\\b/",
                    "/\\bminum/",
                    "/\\bsoda\\b/",
                    "/\\bsusu\\b/",
                    "/\\bsirup\\b/",
                    "/\\bmilkshake\\b/",
                    "/\\bshake\\b/",
                  ];

                  foreach ($drinkPatterns as $pattern) {
                    if (preg_match($pattern, $namaLower) || preg_match($pattern, $deskripsiLower)) {
                      $kategori = 'minuman';
                      break;
                    }
                  }
                ?>
                <div
                  class="menu-item bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
                  data-category="<?php echo $kategori; ?>"
                >
                  <?php if (!empty($imageFile)): ?>
                    <img
                      src="<?php echo $imageSrc; ?>"
                      alt="<?php echo htmlspecialchars($namaProduk ?: 'Produk', ENT_QUOTES, 'UTF-8'); ?>"
                      class="w-full h-48 object-cover"
                    />
                  <?php else: ?>
                    <div class="w-full h-48 bg-gray-100 text-gray-400 flex items-center justify-center text-sm">
                      Tidak ada gambar
                    </div>
                  <?php endif; ?>
                  <div class="p-4">
                    <h3 class="font-bold text-lg mb-2 text-gray-800">
                      <?php echo htmlspecialchars($namaProduk, ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <p class="text-gray-500 text-sm mb-3">
                      <?php echo htmlspecialchars($deskripsiProduk, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-orange-deep font-bold text-xl mb-4">
                      <?php echo formatRupiah($item['harga'] ?? 0); ?>
                    </p>
                    <a
                      href="pembelian.php?id=<?php echo (int) ($item['id'] ?? 0); ?>"
                      class="w-full inline-block bg-orange-deep text-white py-2 rounded-lg hover:bg-orange-600 transition-colors text-center font-semibold"
                    >
                      Beli
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php elseif (!$dbError): ?>
              <div class="col-span-full rounded-2xl border border-orange-deep/20 bg-orange-deep/5 p-10 text-center text-gray-600">
                Menu belum tersedia. Silakan cek kembali nanti.
              </div>
            <?php endif; ?>
          </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-16 bg-milk">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Testimoni Pelanggan
          </h2>
          <p class="text-gray-600 text-lg">
            Apa kata mereka tentang Warung Putra Barokah
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center mb-4">
              <div>
                <h4 class="font-bold">Abah ayat

                </h4>
                <div class="text-yellow-400">â­â­â­â­â­</div>
              </div>
            </div>
            <p class="text-gray-600">
              "Warung Putra Barokah memang mantul! Rasanya enak banget, kuahnya
              gurih, dan pelayanannya ramah. Pasti balik lagi!"
            </p>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center mb-4">
              <div>
                <h4 class="font-bold">Dedeh S</h4>
                <div class="text-yellow-400">â­â­â­â­â­</div>
              </div>
            </div>
            <p class="text-gray-600">
              "Mie ayamnya juara! Porsinya besar, harganya terjangkau. Tempat
              favorit keluarga untuk makan siang."
            </p>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center mb-4">
              <div>
                <h4 class="font-bold">Ahmad Rizki</h4>
                <div class="text-yellow-400">â­â­â­â­â­</div>
              </div>
            </div>
            <p class="text-gray-600">
              "Es buahnya segar banget! Perfect buat cuaca panas. Baksonya juga
              enak, daging asli semua."
            </p>
          </div>
        </div>
      </div>
    </section>

    <section id="location" class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Lokasi Kami
          </h2>
          <p class="text-gray-600 text-lg">
            Datang dan nikmati langsung Warung Putra Barokah di cabang terdekat
            Anda.
          </p>
        </div>
        <div
          class="w-full h-[400px] md:h-[480px] rounded-xl overflow-hidden shadow-lg"
        >
          <iframe
            src="https://www.google.com/maps/embed?pb=!4v1758112547559!6m8!1m7!1sL2vvHI1ki5698cbjn9LObA!2m2!1d-6.782047689201697!2d108.1707409775712!3f252.5613351864675!4f-0.31915517760808143!5f0.7820865974627469"
            width="600"
            height="450"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            class="w-full h-full"
          ></iframe>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-800 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-xl font-bold mb-4 text-orange-deep">
              Warung Putra Barokah
            </h3>
            <p class="text-gray-300">
              Hangatnya Sampe Hati - Menyajikan bakso dan mie ayam terbaik
              dengan cita rasa autentik.
            </p>
          </div>

          <div>
            <h4 class="font-bold mb-4">Menu Favorit</h4>
            <ul class="space-y-2 text-gray-300">
              <li>
                <a href="#menu" class="hover:text-orange-deep transition-colors"
                  >Bakso Urat</a
                >
              </li>
              <li>
                <a href="#menu" class="hover:text-orange-deep transition-colors"
                  >Bakso Tetelan</a
                >
              </li>
              <li>
                <a href="#menu" class="hover:text-orange-deep transition-colors"
                  >Mie Ayam</a
                >
              </li>
              <li>
                <a href="#menu" class="hover:text-orange-deep transition-colors"
                  >Es Buah</a
                >
              </li>
            </ul>
          </div>

          <div>
            <h4 class="font-bold mb-4">Lokasi</h4>
            <ul class="space-y-2 text-gray-300">
              <li>
                <a
                  href="#contact"
                  class="hover:text-orange-deep transition-colors"
                  >Liangjulang, Kadipaten, Majalengka Regency, West Java</a
                >
              </li>
            </ul>
          </div>

          <div>
            <h4 class="font-bold mb-4">Ikuti Kami</h4>
            <ul class="space-y-2 text-gray-300">
              <li>
                <a
                  href="https://www.instagram.com/"
                  class="hover:text-orange-deep transition-colors"
                  target="_blank"
                  rel="noreferrer"
                  >Instagram</a
                >
              </li>
              <li>
                <a
                  href="mailto:radit@gmail.com"
                  class="hover:text-orange-deep transition-colors"
                  >radit@gmail.com</a
                >
              </li>
            </ul>
          </div>
        </div>

        <div
          class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300"
        >
          <p>&copy; 2024 Warung Putra Barokah .</p>
        </div>
      </div>
    </footer>

    <script src="script.js"></script>
  </body>
</html>







