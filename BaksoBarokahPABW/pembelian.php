<?php
include 'connection.php';

function formatRupiah($value)
{
    if (!is_numeric($value)) {
        return 'Rp 0';
    }

    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

function fetchProduct(mysqli $conn, int $productId): ?array
{
    $stmt = $conn->prepare('SELECT id, nama, harga, deskripsi, gambar FROM produk WHERE id = ?');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $product ?: null;
}

$allowedPaymentMethods = [
    'ovo' => 'OVO',
    'dana' => 'Dana',
    'bank' => 'Transfer Bank',
    'qris' => 'QRIS',
    'cod' => 'COD (Bayar di tempat)',
];

$requestedProductId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : (int) ($_GET['id'] ?? 0);
$product = null;
$pageError = '';

if ($requestedProductId > 0) {
    $product = fetchProduct($conn, $requestedProductId);
}

if (!$product) {
    $fallbackResult = $conn->query('SELECT id FROM produk ORDER BY created_at DESC LIMIT 1');
    if ($fallbackResult instanceof mysqli_result) {
        $fallbackRow = $fallbackResult->fetch_assoc();
        $fallbackResult->free();
        if ($fallbackRow) {
            $requestedProductId = (int) $fallbackRow['id'];
            $product = fetchProduct($conn, $requestedProductId);
        }
    }
}

if (!$product) {
    $pageError = 'Produk tidak ditemukan atau belum tersedia. Silakan kembali ke menu utama.';
}

$errors = [];
$customerName = trim($_POST['customerName'] ?? '');
$customerPhone = trim($_POST['customerPhone'] ?? '');
$customerAddress = trim($_POST['customerAddress'] ?? '');
$paymentKeys = array_keys($allowedPaymentMethods);
$defaultPaymentMethod = $paymentKeys ? $paymentKeys[0] : '';
$paymentMethod = $_POST['paymentMethod'] ?? $defaultPaymentMethod;
$paymentNotes = trim($_POST['orderNotes'] ?? '');
$preferredTime = trim($_POST['preferredTime'] ?? '');
$quantity = (int) ($_POST['quantity'] ?? 1);
$quantity = $quantity > 0 ? $quantity : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    if ($customerName === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }

    if ($customerPhone === '') {
        $errors[] = 'Nomor WhatsApp wajib diisi.';
    } elseif (!preg_match('/^[0-9+\s]{8,20}$/', $customerPhone)) {
        $errors[] = 'Format nomor WhatsApp tidak valid.';
    }

    if ($customerAddress === '') {
        $errors[] = 'Alamat lengkap wajib diisi.';
    }

    if (!array_key_exists($paymentMethod, $allowedPaymentMethods)) {
        $errors[] = 'Metode pembayaran tidak valid.';
    }

    if ($quantity < 1) {
        $errors[] = 'Jumlah porsi minimal 1.';
    }

    if (!$errors) {
        $unitPrice = (int) ($product['harga'] ?? 0);
        $totalPrice = $unitPrice * $quantity;
        $paymentNotesValue = $paymentNotes !== '' ? $paymentNotes : null;
        $preferredTimeValue = $preferredTime !== '' ? $preferredTime : null;

        $stmt = $conn->prepare('INSERT INTO orders (
                product_id,
                product_name,
                customer_name,
                customer_phone,
                customer_address,
                payment_method,
                payment_notes,
                preferred_time,
                quantity,
                unit_price,
                total_price
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt) {
            $stmt->bind_param(
                'isssssssiii',
                $product['id'],
                $product['nama'],
                $customerName,
                $customerPhone,
                $customerAddress,
                $paymentMethod,
                $paymentNotesValue,
                $preferredTimeValue,
                $quantity,
                $unitPrice,
                $totalPrice
            );

            if ($stmt->execute()) {
                $orderId = $conn->insert_id;
                $stmt->close();
                $conn->close();
                header('Location: invoice.php?id=' . $orderId);
                exit;
            }

            $errors[] = 'Terjadi kesalahan saat menyimpan pesanan: ' . $stmt->error;
            $stmt->close();
        } else {
            $errors[] = 'Tidak dapat memproses pesanan saat ini.';
        }
    }
}

$unitPrice = (int) ($product['harga'] ?? 0);
$totalPrice = $unitPrice * $quantity;
$productName = $product['nama'] ?? 'Produk';
$productDescription = $product['deskripsi'] ?? '';
$productImage = !empty($product['gambar']) ? 'menu/' . $product['gambar'] : 'https://via.placeholder.com/640x480?text=Produk';
$title = 'Pesan ' . htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') . ' - Warung Putra Barokah';
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title; ?></title>
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
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">
              <?php echo htmlspecialchars('Pesan ' . $productName, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="text-gray-500 mt-2 max-w-xl">
              Isi data berikut untuk memesan <?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?> favorit Anda.
              Setelah dikirim, Anda akan diarahkan ke halaman invoice dan kami segera menghubungi melalui WhatsApp untuk konfirmasi.
            </p>
          </div>
          <a
            href="index.php#menu"
            class="text-orange-deep font-medium hover:underline flex items-center gap-2"
          >
            <span>&larr;</span>
            <span>Kembali ke Website</span>
          </a>
        </div>

        <?php if ($pageError): ?>
          <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-2xl">
            <?php echo htmlspecialchars($pageError, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php elseif ($product): ?>
          <div class="grid gap-10 lg:grid-cols-[minmax(0,420px)_minmax(0,300px)]">
            <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
              <div>
                <h2 class="text-xl font-semibold text-gray-700">Informasi Customer</h2>
                <p class="text-sm text-gray-500 mt-1">
                  Pastikan data sesuai agar kami mudah menghubungi Anda.
                </p>
              </div>

              <?php if ($errors): ?>
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-600 space-y-2">
                  <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <form id="purchase-form" method="post" class="space-y-6">
                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label for="customer-name" class="block text-sm font-medium text-gray-600 mb-2">Nama Lengkap</label>
                    <input
                      id="customer-name"
                      name="customerName"
                      type="text"
                      required
                      value="<?php echo htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8'); ?>"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep focus:border-transparent transition"
                      placeholder="Contoh: Radit Pratama"
                    />
                  </div>
                  <div>
                    <label for="customer-phone" class="block text-sm font-medium text-gray-600 mb-2">Nomor WhatsApp</label>
                    <input
                      id="customer-phone"
                      name="customerPhone"
                      type="tel"
                      required
                      pattern="[0-9+\s]{8,20}"
                      value="<?php echo htmlspecialchars($customerPhone, ENT_QUOTES, 'UTF-8'); ?>"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep focus:border-transparent transition"
                      placeholder="Contoh: 081234567890"
                    />
                  </div>
                </div>

                <div>
                  <label for="customer-address" class="block text-sm font-medium text-gray-600 mb-2">Alamat Lengkap</label>
                  <textarea
                    id="customer-address"
                    name="customerAddress"
                    rows="3"
                    required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep focus:border-transparent transition resize-none"
                    placeholder="Contoh: Jl. Raya Kadipaten No. 12, Majalengka"
                  ><?php echo htmlspecialchars($customerAddress, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="space-y-4">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">
                      Detail Pesanan <?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <div class="flex items-center justify-between bg-orange-50 border border-orange-200 rounded-xl px-4 py-4">
                      <div>
                        <p class="text-sm text-gray-500">Harga per porsi</p>
                        <p id="unit-price" class="text-xl font-bold text-orange-deep">
                          <?php echo formatRupiah($unitPrice); ?>
                        </p>
                      </div>
                      <div class="flex items-center gap-3">
                        <button
                          type="button"
                          id="qty-minus"
                          class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-orange-deep hover:text-orange-deep transition"
                        >
                          -
                        </button>
                        <input
                          id="quantity-input"
                          name="quantity"
                          type="number"
                          min="1"
                          value="<?php echo (int) $quantity; ?>"
                          required
                          class="w-16 text-center px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep"
                        />
                        <button
                          type="button"
                          id="qty-plus"
                          class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:border-orange-deep hover:text-orange-deep transition"
                        >
                          +
                        </button>
                      </div>
                  </div>
                </div>

                <div class="bg-orange-deep/5 border border-orange-deep/30 rounded-xl px-4 py-3 text-sm text-gray-600">
                  <div class="flex items-center justify-between">
                    <span>Perhitungan</span>
                    <span id="calc-expression" class="font-semibold text-gray-800">
                      <?php echo (int) $quantity; ?> x <?php echo formatRupiah($unitPrice); ?>
                    </span>
                  </div>
                  <div class="flex items-center justify-between mt-2">
                    <span>Total Estimasi</span>
                    <span id="calc-total" class="text-lg font-bold text-orange-deep">
                      <?php echo formatRupiah($totalPrice); ?>
                    </span>
                  </div>
                </div>

                <div>
                  <label for="payment-method" class="block text-sm font-medium text-gray-600 mb-2">Metode Pembayaran</label>
                  <select
                    id="payment-method"
                    name="paymentMethod"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep focus:border-transparent transition"
                    >
                      <?php foreach ($allowedPaymentMethods as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $paymentMethod === $value ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div>
                    <label for="order-notes" class="block text-sm font-medium text-gray-600 mb-2">Catatan Pesanan</label>
                    <textarea
                      id="order-notes"
                      name="orderNotes"
                      rows="4"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep focus:border-transparent transition resize-none"
                      placeholder="Contoh: Kuah lebih banyak, tingkat pedas sedang."
                    ><?php echo htmlspecialchars($paymentNotes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                  </div>

                  <div>
                    <label for="preferred-time" class="block text-sm font-medium text-gray-600 mb-2">Waktu pengiriman (opsional)</label>
                    <input
                      id="preferred-time"
                      name="preferredTime"
                      type="text"
                      value="<?php echo htmlspecialchars($preferredTime, ENT_QUOTES, 'UTF-8'); ?>"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-deep focus:border-transparent transition"
                      placeholder="Contoh: 11.30 - 12.00 WIB"
                    />
                  </div>
                </div>

                <div class="space-y-3 text-sm text-gray-500 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3">
                  <p>
                    Setelah mengirim form ini, Anda akan diarahkan ke halaman invoice pembayaran sukses.
                  </p>
                  <p>
                    Jika membutuhkan lebih dari satu menu berbeda, tuliskan rinciannya pada kolom catatan di atas.
                  </p>
                </div>

                <input type="hidden" id="unit-price-input" name="unitPrice" value="<?php echo $unitPrice; ?>" />
                <input type="hidden" id="total-price" name="totalPrice" value="<?php echo $totalPrice; ?>" />

                <button
                  type="submit"
                  class="w-full bg-orange-deep text-white py-3 rounded-full font-semibold hover:bg-orange-600 transition-colors"
                >
                  Kirim Pesanan
                </button>
              </form>
            </div>

            <div class="space-y-6">
              <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <img
                  src="<?php echo htmlspecialchars($productImage, ENT_QUOTES, 'UTF-8'); ?>"
                  alt="<?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>"
                  class="w-full h-48 object-cover"
                />
                <div class="p-6 space-y-4">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                      <?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                      <?php echo htmlspecialchars($productDescription ?: 'Menu spesial dari Warung Putra Barokah.', ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                  </div>
                  <div class="bg-milk rounded-xl px-4 py-3 space-y-2 text-sm text-gray-600">
                    <div class="flex items-center justify-between">
                      <span>Harga per porsi</span>
                      <span class="font-semibold text-orange-deep"><?php echo formatRupiah($unitPrice); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                      <span>Jumlah</span>
                      <span id="summary-qty" class="font-semibold"><?php echo (int) $quantity; ?> porsi</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-semibold text-gray-800">
                      <span>Total</span>
                      <span id="order-total" class="text-orange-deep"><?php echo formatRupiah($totalPrice); ?></span>
                    </div>
                  </div>
                  <p class="text-xs text-gray-400">
                    *Harga belum termasuk ongkos kirim. Pembayaran dapat dilakukan sesuai metode yang Anda pilih.
                  </p>
                </div>
              </div>

              <div class="bg-orange-deep text-white rounded-2xl p-6 space-y-3">
                <h3 class="text-lg font-semibold">Bantuan Cepat</h3>
                <p class="text-sm text-orange-100">
                  Ada pertanyaan? Hubungi kami melalui WhatsApp di
                  <span class="font-semibold">0857-2222-3333</span>.
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <script src="pembelian.js"></script>
  </body>
</html>

 
