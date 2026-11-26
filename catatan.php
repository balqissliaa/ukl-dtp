<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

$pesan = '';
$edit = null;

// Tambah catatan
if (isset($_POST['tambah'])) {
    $id_tugas = $_POST['id_tugas'];
    $isi_catatan = $_POST['isi_catatan'];

    $query = mysqli_prepare($conn, 
        "INSERT INTO catatan (id_tugas, isi_catatan)
         VALUES (?, ?)"
    );

    mysqli_stmt_bind_param($query, "is", $id_tugas, $isi_catatan);
    mysqli_stmt_execute($query);
    mysqli_stmt_close($query);

    header("Location: catatan.php");
    exit;
}

// Mengambil data untuk diedit
if (isset($_GET['edit'])) {
    $id_catatan = $_GET['edit'];

    $query = mysqli_prepare($conn,
        "SELECT * FROM catatan WHERE id_catatan = ?"
    );

    mysqli_stmt_bind_param($query, "i", $id_catatan);
    mysqli_stmt_execute($query);

    $hasil = mysqli_stmt_get_result($query);
    $edit = mysqli_fetch_assoc($hasil);
}

// Update catatan
if (isset($_POST['update'])) {
    $id_catatan = $_POST['id_catatan'];
    $id_tugas = $_POST['id_tugas'];
    $isi_catatan = $_POST['isi_catatan'];

    $query = mysqli_prepare($conn,
        "UPDATE catatan
         SET id_tugas = ?, isi_catatan = ?
         WHERE id_catatan = ?"
    );

    mysqli_stmt_bind_param($query, "isi", $id_tugas, $isi_catatan, $id_catatan);
    mysqli_stmt_execute($query);

    header("Location: catatan.php");
    exit;
}

// Hapus catatan
if (isset($_POST['hapus'])) {
    $id_catatan = $_POST['id_catatan'];

    $query = mysqli_prepare($conn,
        "DELETE FROM catatan WHERE id_catatan = ?"
    );

    mysqli_stmt_bind_param($query, "i", $id_catatan);
    mysqli_stmt_execute($query);

    header("Location: catatan.php");
    exit;
}

// Mengambil daftar tugas
$tugas = mysqli_query($conn,
    "SELECT id_tugas, judul_tugas FROM tugas ORDER BY judul_tugas"
);

// Mengambil daftar catatan dengan JOIN
$catatan = mysqli_query($conn,
    "SELECT catatan.*, tugas.judul_tugas
     FROM catatan
     INNER JOIN tugas ON catatan.id_tugas = tugas.id_tugas
     ORDER BY tanggal_catatan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan - TaskFlow</title>

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

    <h2 class="fw-bold mb-2">Catatan Tugas</h2>
    <p class="text-secondary mb-4">Kelola catatan perkembangan tugasmu.</p>

    <!-- Form Tambah dan Edit -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">
                <?= $edit ? 'Edit Catatan' : 'Tambah Catatan' ?>
            </h5>

            <form method="POST">

                <?php if ($edit): ?>
                    <input type="hidden" name="id_catatan"
                        value="<?= $edit['id_catatan'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Pilih Tugas</label>

                    <select name="id_tugas" class="form-select" required>
                        <option value="">Pilih tugas</option>

                        <?php while ($row = mysqli_fetch_assoc($tugas)): ?>
                            <option value="<?= $row['id_tugas'] ?>"
                                <?= ($edit && $edit['id_tugas'] == $row['id_tugas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['judul_tugas']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Isi Catatan</label>

                    <textarea name="isi_catatan" class="form-control"
                        rows="4" required><?= $edit ? htmlspecialchars($edit['isi_catatan']) : '' ?></textarea>
                </div>

                <?php if ($edit): ?>

                    <button type="submit" name="update" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="catatan.php" class="btn btn-secondary">
                        Batal
                    </a>

                <?php else: ?>

                    <button type="submit" name="tambah" class="btn btn-primary">
                        Tambah Catatan
                    </button>

                <?php endif; ?>

            </form>

        </div>
    </div>

    <!-- Daftar Catatan -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">Daftar Catatan</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Tugas</th>
                            <th>Isi Catatan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($catatan)): ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($row['judul_tugas']) ?>
                                </td>

                                <td>
                                    <?= nl2br(htmlspecialchars($row['isi_catatan'])) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['tanggal_catatan']) ?>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">

                                        <a href="catatan.php?edit=<?= $row['id_catatan'] ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">

                                            <input type="hidden" name="id_catatan"
                                                value="<?= $row['id_catatan'] ?>">

                                            <button type="submit" name="hapus"
                                                class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">
        Kembali ke Dashboard
    </a>

</div>

</body>
</html>