<?php
session_start();

require_once '../connection.php';

if (!empty($_SESSION['admin_logged_in'])) {
  header('Location: dashboard.php');
  exit;
}

$loginMessage = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === 'admin' || $password === 'admin123') {
    $loginMessage = 'Username dan kata sandi wajib diisi.';
  } else {
    require_once '../connection.php';

    $stmt = $conn->prepare(
      'SELECT id, username, password FROM users WHERE username = ? LIMIT 1'
    );

    if ($stmt === false) {
      $loginMessage = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    } else {
      $stmt->bind_param('s', $username);

      if ($stmt->execute()) {
        $result = $stmt->get_result();
        $admin = $result ? $result->fetch_assoc() : null;

        if ($admin && password_verify($password, $admin['password'] ?? '')) {
          $_SESSION['admin_logged_in'] = true;
          $_SESSION['admin_id'] = (int) ($admin['id'] ?? 0);
          $_SESSION['admin_username'] = $admin['username'] ?? $username;

          $stmt->close();
          $conn->close();

          header('Location: dashboard.php');
          exit;
        } else {
          $loginMessage = 'Username atau kata sandi salah.';
        }
      } else {
        $loginMessage = 'Terjadi kesalahan saat memproses data.';
      }

      $stmt->close();
    }

    $conn->close();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin - Warung Putra Barokah</title>
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
  <body class="bg-milk font-body min-h-screen flex items-center">
    <section class="w-full">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div
          class="grid gap-10 md:grid-cols-[1.1fr_1fr] bg-white rounded-3xl shadow-xl overflow-hidden"
        >
          <div
            class="relative hidden md:flex items-end bg-gradient-to-br from-orange-deep to-orange-500 p-8"
          >
            <div class="space-y-4 text-white">
              <span
                class="inline-flex items-center rounded-full bg-white/20 px-4 py-1 text-sm font-medium"
                >Area Admin</span
              >
              <h1 class="text-3xl font-extrabold leading-tight">
                Kelola Warung Putra Barokah dengan lebih mudah dan cepat.
              </h1>
              <p class="text-white/80 text-sm leading-relaxed">
                Masuk menggunakan akun admin yang terdaftar untuk menambah,
                memperbarui, atau menghapus menu favorit pelanggan.
              </p>
            </div>
            <div
              class="absolute -top-12 -right-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"
            ></div>
          </div>

          <div class="p-8 md:p-10">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-800">Login Admin</h2>
                <p class="text-sm text-gray-500 mt-1">
                  Masuk dengan akun yang sudah terdaftar.
                </p>
              </div>
              <a
                href="../index.php"
                class="inline-flex items-center gap-2 text-sm font-semibold text-orange-deep hover:underline"
              >
                <span>&larr;</span>
                <span>Kembali</span>
              </a>
            </div>

            <?php if ($loginMessage !== ''): ?>
              <div
                class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"
              >
                <?php echo htmlspecialchars($loginMessage, ENT_QUOTES, 'UTF-8'); ?>
              </div>
            <?php endif; ?>

            <form method="post" class="space-y-6">
              <div>
                <label
                  for="username"
                  class="block text-sm font-semibold text-gray-700 mb-2"
                  >Username</label
                >
                <input
                  type="text"
                  id="username"
                  name="username"
                  value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                  placeholder="Masukkan username"
                  required
                  class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-gray-700 focus:border-orange-deep focus:ring-2 focus:ring-orange-deep/40 transition"
                />
              </div>

              <div>
                <label
                  for="password"
                  class="block text-sm font-semibold text-gray-700 mb-2"
                  >Kata Sandi</label
                >
                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Masukkan kata sandi"
                  required
                  class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-gray-700 focus:border-orange-deep focus:ring-2 focus:ring-orange-deep/40 transition"
                />
              </div>

              <button
                type="submit"
                class="w-full rounded-full bg-orange-deep px-6 py-3 text-lg font-semibold text-white transition hover:bg-orange-600"
              >
                Masuk Sekarang
              </button>
            </form>
            <p class="mt-6 text-xs text-gray-400">
              Pastikan username dan kata sandi Anda aman dan tidak dibagikan
              kepada orang lain.
            </p>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>
