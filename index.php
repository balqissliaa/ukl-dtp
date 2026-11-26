<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

$total = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS jumlah FROM tugas"))['jumlah'];

$selesai = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS jumlah FROM tugas WHERE status='Selesai'"))['jumlah'];

$belum = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS jumlah FROM tugas WHERE status!='Selesai'"))['jumlah'];

$query = mysqli_query($conn, "
    SELECT tugas.*, kategori.nama_kategori
    FROM tugas
    INNER JOIN kategori
    ON tugas.id_kategori = kategori.id_kategori
    ORDER BY tugas.id_tugas DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskFlow - Dashboard</title>

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

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-secondary">Kelola semua tugasmu dengan mudah.</p>
        </div>

        <a href="tambah.php" class="btn btn-primary">
            + Tambah Tugas
        </a>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card-stat">
                <p>Total Tugas</p>
                <h2><?= $total ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-stat">
                <p>Tugas Selesai</p>
                <h2><?= $selesai ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-stat">
                <p>Belum Selesai</p>
                <h2><?= $belum ?></h2>
            </div>
        </div>

    </div>

    <div class="card p-4 border-0 shadow-sm">
        <h5 class="fw-bold mb-3">Daftar Tugas</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Tugas</th>
                        <th>Prioritas</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php $no = 1; ?>

                <?php while($row = mysqli_fetch_assoc($query)): ?>

                    <tr>
                        <td><?= $no++ ?></td>

                        <td><?= htmlspecialchars($row['judul_tugas']) ?></td>

                        <td><?= htmlspecialchars($row['prioritas']) ?></td>

                        <td><?= $row['deadline'] ?></td>

                        <td>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>

                        <td>
                            <a href="edit.php?id=<?= $row['id_tugas'] ?>"
                               class="btn btn-warning btn-sm">Edit</a>

                            <a href="hapus.php?id=<?= $row['id_tugas'] ?>"
                               onclick="return confirm('Yakin ingin menghapus tugas?')"
                               class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>

                <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
