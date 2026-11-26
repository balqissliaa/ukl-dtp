<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

$kategori = mysqli_query($conn, "SELECT * FROM kategori");

if (isset($_POST['simpan'])) {

    $judul = trim($_POST['judul_tugas']);
    $id_kategori = (int) $_POST['id_kategori'];
    $prioritas = $_POST['prioritas'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn, "
        INSERT INTO tugas
        (id_kategori, judul_tugas, prioritas, deadline, status)
        VALUES (?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt, "issss",
        $id_kategori, $judul,
        $prioritas, $deadline, $status
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            TaskFlow.
        </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarTaskFlow"
            aria-controls="navbarTaskFlow"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTaskFlow">

            
            <div class="navbar-nav ms-auto align-items-lg-center">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="kategori.php">Kategori</a>
                <a class="nav-link" href="catatan.php">Catatan</a>

                <div class="dropdown ms-lg-3">
                    <button class="btn profile-btn dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text fw-semibold">
                                <?= htmlspecialchars($_SESSION['nama']) ?>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


        </div>

    </div>
</nav>

<div class="container py-5">
    <div class="form-card">
        <h3 class="fw-bold mb-4">Tambah Tugas</h3>

        <form method="POST">

            <label class="form-label">Judul Tugas</label>
            <input type="text" name="judul_tugas"
                   class="form-control mb-3" required>

            <label class="form-label">Kategori</label>
            <select name="id_kategori" class="form-select mb-3" required>
                <option value="">Pilih Kategori</option>

                <?php while($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?= $k['id_kategori'] ?>">
                        <?= htmlspecialchars($k['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>

            </select>

            <label class="form-label">Prioritas</label>
            <select name="prioritas" class="form-select mb-3">
                <option>Rendah</option>
                <option selected>Sedang</option>
                <option>Tinggi</option>
            </select>

            <label class="form-label">Deadline</label>
            <input type="date" name="deadline"
                   class="form-control mb-3">

            <label class="form-label">Status</label>
            <select name="status" class="form-select mb-4">
                <option>Belum</option>
                <option>Dikerjakan</option>
                <option>Selesai</option>
            </select>

            <button type="submit" name="simpan"
                    class="btn btn-primary">Simpan</button>

            <a href="index.php" class="btn btn-secondary">Kembali</a>

        </form>
    </div>
</div>

</body>
</html>
