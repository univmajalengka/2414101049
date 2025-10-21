<?php
include 'connection.php';

function formatRupiah($value)
{
    if (!is_numeric($value)) {
        return 'Rp 0';
    }

    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

function formatTanggal(?string $timestamp): string
{
    if (!$timestamp) {
        return '-';
    }

    try {
        $date = new DateTime($timestamp);
    } catch (Exception $e) {
        return $timestamp;
    }

    return $date->format('d F Y, H:i');
}

function generateInvoiceNumber(?string $timestamp, int $id): string
{
    try {
        $date = $timestamp ? new DateTime($timestamp) : new DateTime();
    } catch (Exception $e) {
        $date = new DateTime();
    }

    return sprintf(
        'INV-%s%s%s-%04d',
        $date->format('Y'),
        $date->format('m'),
        $date->format('d'),
        $id
    );
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = null;

if ($orderId > 0) {
    $stmt = $conn->prepare(
        'SELECT o.id,
                o.product_id,
                o.product_name,
                o.customer_name,
                o.customer_phone,
                o.customer_address,
                o.payment_method,
                o.payment_notes,
                o.preferred_time,
                o.quantity,
                o.unit_price,
                o.total_price,
                o.created_at,
                p.gambar AS product_image
         FROM orders o
         LEFT JOIN produk p ON p.id = o.product_id
         WHERE o.id = ?'
    );

    if ($stmt) {
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result ? $result->fetch_assoc() : null;
        $stmt->close();
    }
}

$conn->close();

$paymentLabels = [
    'ovo' => 'OVO',
    'dana' => 'Dana',
    'bank' => 'Transfer Bank',
    'qris' => 'QRIS',
    'cod' => 'COD (Bayar di tempat)',
];

$hasOrder = (bool) $order;
$pageTitle = $hasOrder ? 'Invoice ' . htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8') : 'Invoice Pembelian';
$invoiceNumber = $hasOrder ? generateInvoiceNumber($order['created_at'] ?? null, (int) $order['id']) : '-';
$invoiceDate = $hasOrder ? formatTanggal($order['created_at'] ?? null) : '-';
$productImage = ($order['product_image'] ?? '') !== ''
    ? 'menu/' . $order['product_image']
    : 'https://via.placeholder.com/480x320?text=Produk';
$paymentLabel = $hasOrder ? ($paymentLabels[$order['payment_method']] ?? strtoupper($order['payment_method'] ?? '-')) : '-';
$newOrderLink = $hasOrder && !empty($order['product_id'])
    ? 'pembelian.php?id=' . (int) $order['product_id']
    : 'index.php#menu';
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle; ?> - Warung Putra Barokah</title>
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
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($hasOrder): ?>
          <div class="flex items-start gap-3 mb-8">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl font-bold">
              ✓
            </div>
            <div>
              <h1 class="text-3xl font-bold text-gray-800">Pembayaran Sukses</h1>
              <p class="text-gray-500 mt-1">
                Terima kasih! Pesanan Anda sudah kami terima. Detail pembayaran dan pesanan tercantum pada invoice berikut.
              </p>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
              <div>
                <p class="text-sm text-gray-500">Nomor Invoice</p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($invoiceNumber, ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">Tanggal</p>
                <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($invoiceDate, ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
            </div>

            <div class="px-6 py-5 space-y-6">
              <div class="flex flex-col md:flex-row md:items-start md:gap-6">
                <img
                  src="<?php echo htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8'); ?>"
                  alt="<?php echo htmlspecialchars($order['product_name'] ?? 'Produk', ENT_QUOTES, 'UTF-8'); ?>"
                  class="w-full md:w-48 h-40 rounded-xl object-cover border border-gray-100"
                />
                <div class="flex-1 space-y-2 mt-4 md:mt-0">
                  <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($order['product_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></h2>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 text-sm text-gray-600">
                    <div>
                      <p class="text-gray-500">Nama</p>
                      <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['customer_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div>
                      <p class="text-gray-500">Nomor WhatsApp</p>
                      <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($order['customer_phone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="sm:col-span-2">
                      <p class="text-gray-500">Alamat</p>
                      <p class="font-semibold text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($order['customer_address'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Rincian Pesanan</h2>
                <div class="overflow-hidden rounded-xl border border-gray-100">
                  <table class="min-w-full divide-y divide-gray-100 text-sm text-gray-600">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Jumlah</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white">
                      <tr>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($order['product_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="px-4 py-3"><?php echo (int) ($order['quantity'] ?? 0); ?> porsi</td>
                        <td class="px-4 py-3 text-right"><?php echo formatRupiah($order['unit_price'] ?? 0); ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800"><?php echo formatRupiah($order['total_price'] ?? 0); ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="space-y-3">
                <div class="flex items-center justify-between text-sm text-gray-600">
                  <span>Metode Pembayaran</span>
                  <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($paymentLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="flex items-center justify-between text-lg font-bold text-gray-800">
                  <span>Total Pembayaran</span>
                  <span class="text-orange-deep"><?php echo formatRupiah($order['total_price'] ?? 0); ?></span>
                </div>
              </div>

              <div class="space-y-2 text-sm text-gray-600">
                <div>
                  <p class="font-semibold text-gray-700">Catatan Pesanan</p>
                  <p class="whitespace-pre-line">
                    <?php echo htmlspecialchars($order['payment_notes'] ?: '-', ENT_QUOTES, 'UTF-8'); ?>
                  </p>
                </div>
                <div>
                  <p class="font-semibold text-gray-700">Waktu Pengiriman</p>
                  <p><?php echo htmlspecialchars($order['preferred_time'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
              </div>
            </div>

            <div class="px-6 py-5 bg-gray-50 flex flex-wrap gap-3">
              <button
                type="button"
                id="print-invoice"
                class="inline-flex items-center justify-center gap-2 bg-orange-deep text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-600 transition"
              >
                Cetak Invoice
              </button>
              <a
                href="<?php echo htmlspecialchars($newOrderLink, ENT_QUOTES, 'UTF-8'); ?>"
                class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-600 px-6 py-3 rounded-full font-medium hover:bg-gray-100 transition"
              >
                Buat Pesanan Baru
              </a>
              <a
                href="https://wa.me/6285722223333"
                target="_blank"
                rel="noreferrer"
                class="inline-flex items-center justify-center gap-2 bg-green-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-green-600 transition"
              >
                Hubungi via WhatsApp
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="bg-white rounded-2xl shadow-lg p-6 text-center text-gray-600 space-y-4">
            <h2 class="text-xl font-bold text-gray-800">Belum ada data invoice</h2>
            <p>Silakan lakukan pemesanan terlebih dahulu melalui halaman menu kami.</p>
            <a
              href="index.php#menu"
              class="inline-flex items-center justify-center gap-2 bg-orange-deep text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-600 transition"
            >
              Pergi ke Menu
            </a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <script src="invoice.js"></script>
  </body>
</html>
