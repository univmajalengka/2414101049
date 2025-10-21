<?php
include '../connection.php';

$orders = [];
$dbError = '';

$sql = 'SELECT id, product_id, product_name, customer_name, customer_phone, customer_address, payment_method, payment_notes, preferred_time, quantity, unit_price, total_price, created_at FROM orders ORDER BY created_at DESC';
$result = $conn->query($sql);

if ($result instanceof mysqli_result) {
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
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

function formatOrderDate(?string $datetime): string
{
  if (!$datetime) {
    return '-';
  }

  try {
    $date = new DateTime($datetime);
    return $date->format('d M Y, H:i');
  } catch (Exception $e) {
    return $datetime;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pesanan Masuk - Warung Putra Barokah</title>
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
            <h1 class="text-3xl font-bold text-gray-800">Pesanan Masuk</h1>
            <p class="text-gray-500 mt-2">
              Pantau seluruh transaksi pelanggan Warung Putra Barokah.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <a
              href="dashboard.php"
              class="inline-flex items-center justify-center gap-2 rounded-full bg-orange-deep text-white px-6 py-3 font-semibold hover:bg-orange-600 transition"
            >
              Kembali ke Dashboard
            </a>
            <a
              href="../index.php"
              class="text-orange-deep font-medium hover:underline flex items-center gap-2"
            >
              <span>&larr;</span>
              <span>Website Utama</span>
            </a>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg">
          <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
              <h2 class="text-xl font-semibold text-gray-700">Daftar Pesanan</h2>
              <p class="text-sm text-gray-500">
                Data diurutkan dari pesanan terbaru yang masuk.
              </p>
            </div>
            <span class="text-sm text-gray-500">
              Total pesanan: <strong><?php echo count($orders); ?></strong>
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
                <tr class="bg-gray-100 text-left text-xs sm:text-sm text-gray-600 uppercase">
                  <th class="px-5 py-3 font-semibold">Pesanan</th>
                  <th class="px-5 py-3 font-semibold">Pelanggan</th>
                  <th class="px-5 py-3 font-semibold">Pembayaran</th>
                  <th class="px-5 py-3 font-semibold text-right">Jumlah</th>
                  <th class="px-5 py-3 font-semibold text-right">Total</th>
                  <th class="px-5 py-3 font-semibold text-right">Dibuat</th>
                </tr>
              </thead>
              <tbody class="text-gray-700">
                <?php if (count($orders)): ?>
                  <?php foreach ($orders as $order): ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                      <td class="px-5 py-4 align-top">
                        <div class="font-semibold text-gray-800 text-sm sm:text-base">
                          <?php echo htmlspecialchars($order['product_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 space-y-0.5">
                          <div>ID Pesanan: <?php echo (int) ($order['id'] ?? 0); ?></div>
                          <div>ID Produk: <?php echo (int) ($order['product_id'] ?? 0); ?></div>
                        </div>
                      </td>
                      <td class="px-5 py-4 align-top text-sm leading-relaxed">
                        <div class="font-medium text-gray-800">
                          <?php echo htmlspecialchars($order['customer_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                          <div>Telepon: <?php echo htmlspecialchars($order['customer_phone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                          <div class="mt-1">
                            <?php echo nl2br(htmlspecialchars($order['customer_address'] ?? '-', ENT_QUOTES, 'UTF-8')); ?>
                          </div>
                        </div>
                      </td>
                      <td class="px-5 py-4 align-top text-sm">
                        <div class="inline-flex items-center rounded-full bg-orange-deep/10 px-3 py-1 text-xs font-semibold text-orange-deep uppercase tracking-wide">
                          <?php echo htmlspecialchars($order['payment_method'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php if (!empty($order['payment_notes'])): ?>
                          <div class="mt-3 text-xs text-gray-500">
                            Catatan: <?php echo nl2br(htmlspecialchars($order['payment_notes'], ENT_QUOTES, 'UTF-8')); ?>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($order['preferred_time'])): ?>
                          <div class="mt-2 text-xs text-gray-500">
                            Waktu pilihan: <?php echo htmlspecialchars($order['preferred_time'], ENT_QUOTES, 'UTF-8'); ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="px-5 py-4 align-top text-right text-sm">
                        <div class="font-semibold text-gray-800">
                          <?php echo (int) ($order['quantity'] ?? 0); ?> porsi
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                          <?php echo formatRupiah($order['unit_price'] ?? 0); ?> / porsi
                        </div>
                      </td>
                      <td class="px-5 py-4 align-top text-right text-sm">
                        <div class="inline-flex items-center justify-end rounded-full bg-emerald-500/10 px-3 py-1 font-semibold text-emerald-600">
                          <?php echo formatRupiah($order['total_price'] ?? 0); ?>
                        </div>
                      </td>
                      <td class="px-5 py-4 align-top text-right text-xs text-gray-500">
                        <?php echo htmlspecialchars(formatOrderDate($order['created_at'] ?? null), ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-500 text-sm">
                      Belum ada pesanan yang tercatat. Pesanan yang masuk akan muncul secara otomatis di sini.
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
