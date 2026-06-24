<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'DKA') {
    header("Location: ../../../index.php");
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Data Karyawan</h2>
        <p class="text-secondary small">Kelola data karyawan yang terdaftar di sistem EVORIA</p>
    </div>
    <a href="dashboard.php?page=karyawan_create" class="btn btn-purple rounded-pill px-4">
        <i class="fa fa-plus me-2"></i> Tambah Karyawan
    </a>
</div>

<div class="card p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #2d1b4e;">
            <i class="fa fa-list me-2" style="color: #a286f4;"></i> Daftar Karyawan
        </h5>
        <span class="badge bg-purple text-white rounded-pill px-3 py-2">
            Total: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM karyawan")) ?> Data
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>NID</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY id_karyawan DESC");
                $no = 1;
                if (mysqli_num_rows($query) > 0):
                    while($row = mysqli_fetch_array($query)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['nid']) ?></td>
                    <td><?= htmlspecialchars($row['namaKrw']) ?></td>
                    <td>
                        <span class="badge bg-primary rounded-pill px-3">
                            <?= htmlspecialchars($row['jabatan']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="dashboard.php?page=karyawan_update&id_karyawan=<?= $row['id_karyawan'] ?>" class="btn btn-outline-primary btn-sm rounded-3 me-1">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="dashboard.php?page=karyawan_delete&id_karyawan=<?= $row['id_karyawan'] ?>" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Hapus data karyawan ini?');">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fa fa-inbox d-block fs-2 mb-2"></i>
                        Belum ada data karyawan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>