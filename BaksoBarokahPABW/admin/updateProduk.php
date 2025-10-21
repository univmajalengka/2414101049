<?php
include '../connection.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$row = null;

if ($id) {
  $sql = "SELECT * FROM produk WHERE id=$id";
  $result = $conn->query($sql);
  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
  } else {
    echo "Data tidak ditemukan!";
    exit();
  }
}

if (isset($_POST['update'])) {
  $id = (int) ($_POST['id'] ?? 0);
  $nama = $_POST['nama'] ?? '';
  $deskripsi = $_POST['deskripsi'] ?? '';
  $harga = $_POST['harga'] ?? '';
  $gambar = $_FILES['gambar']['name'] ?? '';
  $tmpGambar = $_FILES['gambar']['tmp_name'] ?? '';
  $errorUpload = $_FILES['gambar']['error'] ?? UPLOAD_ERR_NO_FILE;

  if (!empty($gambar)) {
    if ($errorUpload === UPLOAD_ERR_OK) {
      $uploadDir = realpath(__DIR__ . '/../menu');
      if ($uploadDir === false) {
        $uploadDir = __DIR__ . '/../menu';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0775, true);
        }
      }
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
      }
      $targetFile = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($gambar);

      // ambil gambar lama
      $sql = "SELECT gambar FROM produk WHERE id=$id";
      $result = $conn->query($sql);
      $old = $result->fetch_assoc();
      $old_image = $old['gambar'];

      if (move_uploaded_file($tmpGambar, $targetFile)) {
        $sql = "UPDATE produk 
                    SET nama='$nama', deskripsi='$deskripsi', harga='$harga', gambar='$gambar' 
                    WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
          $oldPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $old_image;
          if ($old_image && file_exists($oldPath)) {
            unlink($oldPath);
          }
          echo "<script>alert(' berhasil diperbarui!');window.location.href='dashboard.php';</script>";
        } else {
          echo "Error: " . $conn->error;
        }
      } else {
        echo "Gagal upload gambar.";
      }
    } else {
      echo "Gagal upload gambar.";
    }
  } else {
    $sql = "UPDATE produk SET nama='$nama', deskripsi='$deskripsi', harga='$harga' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
      echo "<script>alert(' berhasil diperbarui!');window.location.href='dashboard.php';</script>";
    } else {
      echo "Error: " . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Produk - Warung Putra Barokah</title>
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
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Produk</h1>
            <p class="text-gray-500 mt-2">
              Perbarui detail produk Warung Putra Barokah sesuai kebutuhan.
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
            action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            method="post"
            enctype="multipart/form-data"
            class="grid gap-8 md:grid-cols-[1fr,1.2fr]"
          >
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />

            <div class="space-y-6">
              <div>
                <p class="text-sm font-semibold text-gray-600 mb-3">
                  Pratinjau Gambar Saat Ini
                </p>
                <div
                  class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gray-50"
                >
                  <img
                    src="<?php echo !empty($row['gambar']) ? '../menu/' . htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/480x320?text=Gambar+Produk'; ?>"
                    alt="<?php echo htmlspecialchars($row['nama'] ?? 'Gambar produk', ENT_QUOTES, 'UTF-8'); ?>"
                    class="w-full h-56 object-cover"
                    id="preview-gambar"
                  />
                </div>
                <p class="text-xs text-gray-500 mt-3">
                  Unggah gambar baru hanya jika ingin mengganti gambar produk.
                </p>
              </div>

              <div>
                <label
                  for="gambar"
                  class="block text-sm font-semibold text-gray-700 mb-2"
                >
                  Gambar Produk (opsional)
                </label>
                <input
                  type="file"
                  id="gambar"
                  name="gambar"
                  accept="image/*"
                  class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-deep file:text-white file:font-medium hover:file:bg-orange-600"
                />
              </div>
            </div>

            <div class="space-y-6">
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
                  value="<?php echo htmlspecialchars($row['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
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
                  value="<?php echo htmlspecialchars($row['harga'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
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
                ><?php echo htmlspecialchars($row['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              </div>

              <button
                type="submit"
                name="update"
                class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-orange-deep px-6 py-3 text-lg font-semibold text-white transition hover:bg-orange-600"
              >
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>

    <script>
      const inputGambar = document.getElementById("gambar");
      const previewGambar = document.getElementById("preview-gambar");

      if (inputGambar && previewGambar) {
        inputGambar.addEventListener("change", (event) => {
          const file = event.target.files?.[0];
          if (!file) return;

          const reader = new FileReader();
          reader.onload = (e) => {
            previewGambar.src = e.target?.result ?? previewGambar.src;
          };
          reader.readAsDataURL(file);
        });
      }
    </script>
  </body>
</html>
