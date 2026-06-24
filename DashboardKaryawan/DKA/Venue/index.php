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
        <h2 class="fw-bold" style="color: #2d1b4e;">Data Venue</h2>
        <p class="text-secondary small">Kelola data venue/lokasi acara yang tersedia</p>
    </div>
    <a href="dashboard.php?page=venue_create" class="btn btn-purple rounded-pill px-4">
        <i class="fa fa-plus me-2"></i> Tambah Venue
    </a>
</div>

<div class="card p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #2d1b4e;">
            <i class="fa fa-list me-2" style="color: #a286f4;"></i> Daftar Venue
        </h5>
        <span class="badge bg-purple text-white rounded-pill px-3 py-2">
            Total: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM venue WHERE deleted_at IS NULL")) ?> Data
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama Venue</th>
                    <th>Lokasi</th>
                    <th class="text-center">Kapasitas</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $v = mysqli_query($conn, "SELECT * FROM venue WHERE deleted_at IS NULL ORDER BY id_venue DESC");
                $no = 1;
                if (mysqli_num_rows($v) > 0):
                    while($rv = mysqli_fetch_array($v)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($rv['namaV']) ?></td>
                    <td><i class="fa fa-map-pin me-1 text-muted"></i> <?= htmlspecialchars($rv['lokasi']) ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark rounded-pill px-3">
                            <i class="fa fa-users me-1"></i> <?= number_format($rv['kapasitas']) ?> Orang
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="dashboard.php?page=venue_update&id_venue=<?= $rv['id_venue'] ?>" class="btn btn-outline-primary btn-sm rounded-3 me-1">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="dashboard.php?page=venue_delete&id_venue=<?= $rv['id_venue'] ?>" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Hapus venue ini?');">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fa fa-inbox d-block fs-2 mb-2"></i>
                        Belum ada data venue.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>