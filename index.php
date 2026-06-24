<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>EVORIA | Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b0b1a; color: white; font-family: sans-serif; overflow-x: hidden; }
        .navbar { background: transparent; padding: 20px 0; }
        .hero-section { padding: 150px 0; }
        .hero-title { font-size: 64px; font-weight: bold; line-height: 1.1; }
        .text-accent { color: #a286f4; }
        .btn-purple { background-color: #4e29a3; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; }
        .nav-login-btn { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 8px 20px; border-radius: 8px; margin-left: 10px; }
        .modal-content { background-color: #1a1a2e; color: white; border: 1px solid #303056; }
        .form-control { background: #0f0f1e; border: 1px solid #303056; color: white; }
        .footer-section { background-color: #141414; padding: 50px 0; margin-top: 100px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand text-white fw-bold fs-3 d-flex align-items-center" href="#">
                <img src="asset/logo.png" alt="EVORIA" style="height: 90px; width: 150px; margin-right: 12px;">
            </a>
            <div class="ms-auto">
                <button class="nav-login-btn" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="setRole('Mahasiswa')"><i class="fa fa-user-graduate me-2"></i> Mahasiswa</button>
                <button class="nav-login-btn" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="setRole('Umum')"><i class="fa fa-users me-2"></i> Umum</button>
                <button class="nav-login-btn" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="setRole('DKA')"><i class="fa fa-user-shield me-2"></i> Karyawan</button>
            </div>
        </div>
    </nav>

    <section class="hero-section mt-5">
        <div class="container">
            <h1 class="hero-title">Transform your event<br>experience with <span class="text-accent">Evoria.</span></h1>
            <p class="text-accent fw-bold mt-3">by HIMA TRPL</p>
            <button class="btn btn-purple btn-lg mt-4 px-5" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="setRole('Mahasiswa')">Mulai Sekarang</button>
        </div>
    </section>

    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6"><h5>EVORIA</h5><p class="text-secondary small">Ubah pengalaman acara Anda dengan platform yang mulus.</p></div>
                <div class="col-md-6"><h5>ADDRESS</h5><p class="text-secondary small">Jl. Gaharu Blok F3, Cikarang Selatan, Bekasi.</p></div>
            </div>
        </div>
    </footer>

    <!-- MODAL LOGIN -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-4 shadow-lg">
                <h4 class="text-center fw-bold">EVO<span class="text-accent">RIA</span></h4>
                <p class="text-center small text-secondary">Login <span id="roleTitle" class="text-accent"></span></p>
                <form action="proses_login.php" method="POST">
                    <input type="hidden" name="role" id="inputRole">
                    <div class="mb-3">
                        <label class="small mb-1">NID / NIM / Username</label>
                        <input type="text" name="username" class="form-control py-2" placeholder="Masukkan ID" required>
                    </div>
                    <div class="mb-3">
                        <label class="small mb-1">Password</label>
                        <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-purple w-100 py-2 mt-3">Masuk</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setRole(role) {
            document.getElementById('roleTitle').innerText = role;
            document.getElementById('inputRole').value = role;
        }
    </script>
</body>
</html>