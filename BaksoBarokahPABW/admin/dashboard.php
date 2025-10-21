<?php
include '../connection.php';

$products = [];
$dbError = '';

$sql = "SELECT id, nama, harga, deskripsi, gambar, created_at FROM produk ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
    <title>Dashboard Admin - Warung Putra Barokah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
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
  <body class="bg-milk font-body min-h-screen">
    <section class="py-12 md:py-16">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-gray-500 mt-2">
              Kelola produk Warung Putra Barokah secara cepat dan mudah.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <a
              href="orders.php"
              class="inline-flex items-center justify-center gap-2 bg-orange-deep text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-600 transition"
            >
              Lihat Pemesanan
            </a>
            <a
              href="createProduk.php"
              class="inline-flex items-center justify-center gap-2 bg-orange-deep text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-600 transition"
            >
              Tambah Produk
            </a>
            <a
              href="../index.php"
              class="text-orange-deep font-medium hover:underline flex items-center gap-2"
            >
              <span>&larr;</span>
              <span>Kembali ke Website</span>
            </a>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg">
          <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
              <h2 class="text-xl font-semibold text-gray-700">Daftar Produk</h2>
              <p class="text-sm text-gray-500">
                Edit atau hapus produk kapan saja sesuai kebutuhan.
              </p>
            </div>
            <span class="text-sm text-gray-500">
              Total produk: <strong><?php echo count($products); ?></strong>
            </span>
          </div>

          <?php if ($dbError): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600 mb-6">
              Terjadi kesalahan saat memuat data: <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
              <thead>
                <tr class="bg-gray-100 text-left text-sm text-gray-600 uppercase">
                  <th class="px-6 py-3 font-semibold">Nama</th>
                  <th class="px-6 py-3 font-semibold">Harga</th>
                  <th class="px-6 py-3 font-semibold">Deskripsi</th>
                  <th class="px-6 py-3 font-semibold">Gambar</th>
                  <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="text-gray-700">
                <?php if (count($products)): ?>
                  <?php foreach ($products as $product): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                      <td class="px-6 py-4 align-top">
                        <div class="font-semibold text-gray-800">
                          <?php echo htmlspecialchars($product['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                          ID: <?php echo (int) ($product['id'] ?? 0); ?>
                        </div>
                      </td>
                      <td class="px-6 py-4 align-top">
                        <span class="inline-flex items-center rounded-full bg-orange-deep/10 px-3 py-1 text-sm font-semibold text-orange-deep">
                          <?php echo formatRupiah($product['harga'] ?? 0); ?>
                        </span>
                      </td>
                      <td class="px-6 py-4 align-top text-sm text-gray-600">
                        <?php echo nl2br(htmlspecialchars($product['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
                      </td>
                      <td class="px-6 py-4 align-top">
                        <?php if (!empty($product['gambar'])): ?>
                          <img
                            src="<?php echo '../menu/' . htmlspecialchars($product['gambar'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($product['nama'] ?? 'Gambar produk', ENT_QUOTES, 'UTF-8'); ?>"
                            class="h-16 w-24 rounded-lg object-cover shadow-sm"
                          />
                        <?php else: ?>
                          <div class="h-16 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                            Tidak ada gambar
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="px-6 py-4 align-top">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 justify-center">
                          <a
                            href="updateProduk.php?id=<?php echo (int) ($product['id'] ?? 0); ?>"
                            class="inline-flex items-center justify-center rounded-full bg-orange-deep/10 px-4 py-2 text-sm font-semibold text-orange-deep hover:bg-orange-deep hover:text-white transition"
                          >
                            Edit
                          </a>
                          <a
                            href="deleteProduk.php?id=<?php echo (int) ($product['id'] ?? 0); ?>"
                            class="inline-flex items-center justify-center rounded-full bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-500 hover:bg-red-500 hover:text-white transition"
                            onclick="return confirm('Yakin ingin menghapus produk ini?');"
                          >
                            Hapus
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                      Belum ada produk yang tersimpan. Mulai dengan menambahkan produk baru.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>
