<?php
$judul = "Daftar";
require_once __DIR__ . '/../config/koneksi.php';

// Kalau sudah login, tidak perlu daftar lagi
if (isset($_SESSION['user'])) {
    header("Location: /toko-sepatu/toko/index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $no_telpon = trim($_POST['no_telpon']);
    $alamat    = trim($_POST['alamat']);
    $password  = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $errors[] = "Username 3-30 karakter, hanya huruf, angka, dan garis bawah.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter.";
    }
    if ($password !== $konfirmasi) {
        $errors[] = "Konfirmasi password tidak sama.";
    }

    if (!$errors) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO `user` (username, password, email, no_telpon, alamat, role)
                 VALUES (?, ?, ?, ?, ?, 'customer')");
            mysqli_stmt_bind_param($stmt, "sssss", $username, $hash, $email, $no_telpon, $alamat);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Pendaftaran berhasil. Silakan login.";
            header("Location: login.php");
            exit;
        } catch (mysqli_sql_exception $ex) {
            $errors[] = "Username atau email sudah dipakai.";
        }
    }
}

include __DIR__ . '/partials/header.php';
?>

<div class="container my-5" style="max-width:520px">
  <h1 class="h3 fw-bold mb-4">Daftar Akun</h1>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" value="<?= e($_POST['username'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">No. Telpon</label>
      <input type="text" name="no_telpon" class="form-control" value="<?= e($_POST['no_telpon'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Alamat Pengiriman</label>
      <textarea name="alamat" class="form-control" rows="3"><?= e($_POST['alamat'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Password (minimal 8 karakter)</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Ulangi Password</label>
      <input type="password" name="konfirmasi" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-dark w-100">Daftar</button>
  </form>

  <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Login</a></p>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>