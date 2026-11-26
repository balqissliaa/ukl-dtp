<?php
require_once 'config/koneksi.php';

if (isset($_POST['daftar'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = mysqli_prepare($conn,
        "INSERT INTO users (nama, username, password)
         VALUES (?, ?, ?)");

    mysqli_stmt_bind_param($query, "sss", $nama, $username, $password);

    try {
    if (mysqli_stmt_execute($query)) {
        header("Location: login.php");
        exit;
    }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error = "Username sudah digunakan! Silakan gunakan username lain.";
        } else {
            $error = "Terjadi kesalahan saat mendaftar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 450px;">
    <div class="card p-4">
        <h3 class="text-center mb-4">Daftar TaskFlow</h3>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="nama" class="form-control mb-3"
                placeholder="Nama lengkap" required>

            <input type="text" name="username" class="form-control mb-3"
                placeholder="Username" required>

            <input type="password" name="password" class="form-control mb-3"
                placeholder="Password" required>

            <button type="submit" name="daftar" class="btn btn-primary w-100">
                Daftar
            </button>
        </form>

        <p class="text-center mt-3">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>
    </div>
</div>
</body>
</html>