<?php
$judul = "Login";
require_once __DIR__ . '/../config/koneksi.php';

$tokoUrl = '/toko-sepatu/toko';

// Tentukan tujuan setelah login
$next   = $_GET['next'] ?? '';
$tujuan = (strpos($next, $tokoUrl . '/') === 0) ? $next : $tokoUrl . '/index.php';

// Kalau sudah login, langsung arahkan
if (isset($_SESSION['user'])) {
    header("Location: $tujuan");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi,
        "SELECT kd_user, username, password, role FROM `user`
         WHERE username = ? OR email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $login, $login);
    mysqli_stmt_execute($stmt);
    $u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($u && password_verify($password, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'kd_user'  => $u['kd_user'],
            'username' => $u['username'],
            'role'     => $u['role'],
        ];
        header("Location: $tujuan");
        exit;
    }
    $error = "Username/email atau password salah.";
}

include __DIR__ . '/partials/header.php';
?>

<div class="container my-5" style="max-width:420px">
  <h1 class="h3 fw-bold mb-4">Login</h1>

  <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-success"><?= e($_SESSION['pesan']) ?></div>
    <?php unset($_SESSION['pesan']); ?>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Username atau Email</label>
      <input type="text" name="login" class="form-control" value="<?= e($_POST['login'] ?? '') ?>" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-dark w-100">Login</button>
  </form>

  <p class="text-center mt-3 mb-0">Belum punya akun? <a href="daftar.php">Daftar</a></p>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>