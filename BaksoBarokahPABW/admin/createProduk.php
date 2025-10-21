<?php
if (isset($_POST['create'])) {
  include '../connection.php';

  $nama = $_POST['nama'];
  $deskripsi = $_POST['deskripsi'];
  $harga = $_POST['harga'];
  $gambar = $_FILES['gambar']['name'] ?? '';
  $tempGambar = $_FILES['gambar']['tmp_name'] ?? '';
  $errorUpload = $_FILES['gambar']['error'] ?? UPLOAD_ERR_NO_FILE;

  $target_dir = realpath(__DIR__ . '/../menu');
  if ($target_dir === false) {
    $target_dir = __DIR__ . '/../menu';
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0775, true);
    }
  }

  $target_file = rtrim($target_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($gambar);
  if (!is_writable($target_dir)) {
    echo "Folder tidak upload tidak diizinkan: $target_dir";
    exit;
  }

  if ($errorUpload === UPLOAD_ERR_OK && move_uploaded_file($tempGambar, $target_file)) {
    $sql = "INSERT INTO produk (nama, deskripsi, harga, gambar) VALUES ('$nama', '$deskripsi', '$harga', '$gambar')";

    if ($conn->query($sql) === TRUE) {
      echo "<script>alert('Data berhasil disimpan.'); window.location.href='dashboard.php';</script>";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  } else {
    echo "Error uploading file: " . $errorUpload;
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Produk - Warung Putra Barokah</title>
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
        <div class="flex items-center justify-between mb-10">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Produk</h1>
            <p class="text-gray-500 mt-2">
              Lengkapi detail produk Warung Putra Barokah sebelum disimpan.
            </p>
          </div>
          <a
            href="dashboard.php"
            class="inline-flex items-center gap-2 text-orange-deep font-semibold hover:underline"
          >
            <span>&larr;</span>
            <span>Kembali</span>
          </a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-lg">
          <form
            action=""
            method="post"
            enctype="multipart/form-data"
            class="space-y-6"
          >
            <div>
              <label
                for="gambar"
                class="block text-sm font-semibold text-gray-700 mb-2"
              >
                Gambar Produk
              </label>
              <input
                type="file"
                id="gambar"
                name="gambar"
                accept="image/*"
                required
                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-deep file:text-white file:font-medium hover:file:bg-orange-600"
              />
            </div>

            <div>
              <label
                for="nama"
                class="block text-sm font-semibold text-gray-700 mb-2"
              >
                Nama Produk
              </label>
              <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Contoh: Bakso Spesial"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 focus:border-orange-deep focus:ring-2 focus:ring-orange-deep/40 transition"
              />
            </div>

            <div>
              <label
                for="harga"
                class="block text-sm font-semibold text-gray-700 mb-2"
              >
                Harga
              </label>
              <input
                type="number"
                id="harga"
                name="harga"
                placeholder="Contoh: 20000"
                min="0"
                step="100"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 focus:border-orange-deep focus:ring-2 focus:ring-orange-deep/40 transition"
              />
            </div>

            <div>
              <label
                for="deskripsi"
                class="block text-sm font-semibold text-gray-700 mb-2"
              >
                Deskripsi
              </label>
              <textarea
                id="deskripsi"
                name="deskripsi"
                placeholder="Tuliskan deskripsi singkat produk"
                required
                rows="5"
                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 focus:border-orange-deep focus:ring-2 focus:ring-orange-deep/40 transition"
              ></textarea>
            </div>

            <button
              type="submit"
              name="create"
              class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-orange-deep px-6 py-3 text-lg font-semibold text-white transition hover:bg-orange-600"
            >
              Simpan Produk
            </button>
          </form>
        </div>
      </div>
    </section>
  </body>
</html>
