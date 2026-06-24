<?php
include '../../Config/koneksi.php';
/** @var mysqli $conn **/

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Ketua Hima', 'Wakil Ketua Hima', 'Sekretaris Hima', 'Bendahara Hima'])) {
    header("Location: ../../../index.php");
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold" style="color: #2d1b4e;">Jabatan HIMA</h2>
        <p class="text-secondary small">Kelola jabatan mahasiswa di HIMA</p>
    </div>
    <a href="dashboard.php?page=jabatan_create" class="btn btn-purple rounded-pill px-4">
        <i class="fa fa-plus me-2"></i> Tambah Jabatan
    </a>
</div>

<div class="card p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #2d1b4e;">
            <i class="fa fa-list me-2" style="color: #a286f4;"></i> Daftar Jabatan
        </h5>
        <span class="badge bg-purple text-white rounded-pill px-3 py-2">
            Total: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM jabatan_hima WHERE is_active = 1")) ?> Data
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Periode</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // PERBAIKI: pakai namaDepartemen (bukan nama_departemen)
                $query = "SELECT j.*, m.nim, m.nama, d.namaDepartemen AS nama_departemen 
                          FROM jabatan_hima j 
                          LEFT JOIN mahasiswa m ON j.idMhs = m.idMhs
                          LEFT JOIN departemen d ON j.id_departemen = d.id_departemen
                          WHERE j.is_active = 1
                          ORDER BY j.id_jabatan_hima DESC";
                $result = mysqli_query($conn, $query);
                $no = 1;
                
                if (mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_array($result)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nim']) ?></td>
                    <td class="fw-bold" style="color: #2d1b4e;"><?= htmlspecialchars($row['nama']) ?></td>
                    <td>
                        <?php 
                        $badgeJabatan = 'bg-info';
                        if($row['nama_jabatan'] == 'Ketua Hima') $badgeJabatan = 'bg-danger';
                        elseif($row['nama_jabatan'] == 'Wakil Ketua Hima') $badgeJabatan = 'bg-warning text-dark';
                        elseif($row['nama_jabatan'] == 'Sekretaris Hima') $badgeJabatan = 'bg-success';
                        elseif($row['nama_jabatan'] == 'Bendahara Hima') $badgeJabatan = 'bg-primary';
                        elseif(strpos($row['nama_jabatan'], 'Kadep') !== false) $badgeJabatan = 'bg-secondary';
                        ?>
                        <span class="badge <?= $badgeJabatan ?> rounded-pill px-3">
                            <?= htmlspecialchars($row['nama_jabatan']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['nama_departemen'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['periode']) ?></td>
                    <td class="text-center">
                        <?php if ($row['is_active'] == 1): ?>
                            <span class="badge bg-success rounded-pill">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary rounded-pill">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="dashboard.php?page=jabatan_update&id=<?= $row['id_jabatan_hima'] ?>" class="btn btn-outline-primary btn-sm rounded-3 me-1">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="dashboard.php?page=jabatan_delete&id=<?= $row['id_jabatan_hima'] ?>" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Hapus jabatan ini?');">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fa fa-inbox d-block fs-2 mb-2"></i>
                        Belum ada data jabatan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>