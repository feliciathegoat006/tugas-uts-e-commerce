<?php
require_once 'config/koneksi.php';
$base = '/toko-sepatu';

// Kalau sudah login sebagai admin, langsung ke dashboard
if (($_SESSION['user']['role'] ?? '') === 'admin') {
    header("Location: $base/index.php");
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
        if ($u['role'] !== 'admin') {
            // Nanti pelanggan diarahkan ke halaman toko
            $error = "Akun ini bukan admin.";
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'kd_user'  => $u['kd_user'],
                'username' => $u['username'],
                'role'     => $u['role'],
            ];
            header("Location: $base/index.php");
            exit;
        }
    } else {
        $error = "Username/email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin | Footstep</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= $base ?>/css/adminlte.css">
</head>
<body class="login-page bg-body-secondary">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <h1 class="mb-0"><b>foot</b> Step</h1>
    </div>
    <div class="card-body login-card-body">
      <p class="login-box-msg">Login untuk masuk ke panel admin</p>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="input-group mb-3">
          <input type="text" name="login" class="form-control" placeholder="Username atau email"
                 value="<?= e($_POST['login'] ?? '') ?>" required autofocus>
          <div class="input-group-text"><span class="bi bi-person"></span></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>