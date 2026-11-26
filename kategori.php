<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_kategori']);
    $deskripsi = trim($_POST['deskripsi']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ss", $nama, $deskripsi);
    mysqli_stmt_execute($stmt);

    header("Location: kategori.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = (int) $_POST['id_kategori'];
    $nama = trim($_POST['nama_kategori']);
    $deskripsi = trim($_POST['deskripsi']);

    $stmt = mysqli_prepare($conn,
        "UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id_kategori=?"
    );
    mysqli_stmt_bind_param($stmt, "ssi", $nama, $deskripsi, $id);
    mysqli_stmt_execute($stmt);

    header("Location: kategori.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM kategori WHERE id_kategori=?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php");
        exit;
    }

    $pesan = "Kategori masih digunakan oleh tugas dan tidak bisa dihapus.";
}

$edit = null;

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM kategori WHERE id_kategori=?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$data = mysqli_query($conn, "
    SELECT kategori.*, COUNT(tugas.id_tugas) AS jumlah_tugas
    FROM kategori
    LEFT JOIN tugas ON kategori.id_kategori = tugas.id_kategori
    GROUP BY kategori.id_kategori
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kategori TaskFlow</title>
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

    <h2 class="fw-bold mb-4">Kelola Kategori</h2>

    <?php if(isset($pesan)): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <div class="form-card mb-4">
        <form method="POST">

            <?php if($edit): ?>
                <input type="hidden" name="id_kategori"
                       value="<?= $edit['id_kategori'] ?>">
            <?php endif; ?>

            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori"
                class="form-control mb-3"
                value="<?= htmlspecialchars($edit['nama_kategori'] ?? '') ?>"
                required>

            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi"
                class="form-control mb-3"><?= htmlspecialchars($edit['deskripsi'] ?? '') ?></textarea>

            <?php if($edit): ?>
                <button name="update" class="btn btn-primary">Update</button>
                <a href="kategori.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button name="simpan" class="btn btn-primary">Tambah Kategori</button>
            <?php endif; ?>

        </form>
    </div>

    <div class="form-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Tugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td><?= $row['jumlah_tugas'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['id_kategori'] ?>"
                               class="btn btn-warning btn-sm">Edit</a>

                            <a href="?hapus=<?= $row['id_kategori'] ?>"
                               onclick="return confirm('Hapus kategori?')"
                               class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>

</div>
</body>
</html>
