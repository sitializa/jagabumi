<?php
$auth = new AuthController();
$auth->register();
$error = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Eco-Bridge</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f0f7f0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .card { border-radius: 16px; border: none; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .btn-eco { background: #1a7a4a; color: #fff; border: none; }
    .btn-eco:hover { background: #155c38; color: #fff; }
    .brand { color: #1a7a4a; font-weight: 700; font-size: 1.5rem; }
  </style>
</head>
<body>
  <div class="card p-4" style="width:100%;max-width:480px;">
    <div class="text-center mb-4">
      <div class="brand">♻️ Eco-Bridge</div>
      <p class="text-muted small">Buat akun baru</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Lengkap</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Password</label>
        <input type="password" name="password" class="form-control" minlength="6" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Daftar sebagai</label>
        <select name="role" class="form-select" required>
          <option value="mahasiswa">🎓 Mahasiswa (Penyedia Limbah)</option>
          <option value="umkm">🏭 UMKM / Industri Daur Ulang</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Kampus / Perusahaan</label>
        <input type="text" name="institution" class="form-control" placeholder="Contoh: Universitas Brawijaya">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">No. HP</label>
        <input type="text" name="phone" class="form-control" placeholder="08xx">
      </div>
      <button type="submit" class="btn btn-eco w-100 fw-semibold py-2">Daftar Sekarang</button>
    </form>

    <p class="text-center text-muted small mt-3">
      Sudah punya akun? <a href="?url=login" class="text-success">Login di sini</a>
    </p>
  </div>
</body>
</html>