<?php
$itemController = new ItemController();
$itemController->store();

$trxController = new TransactionController();
$trxController->respond();

$item     = new Item();
$trx      = new Transaction();
$myItems  = $item->getItemsByUser($_SESSION['user_id']);
$myTrx    = $trx->getByMahasiswa($_SESSION['user_id']);
$stats    = $item->getStatsByUser($_SESSION['user_id']);
$success  = Session::getFlash('success');
$error    = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Mahasiswa | Eco-Bridge</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f0f7f0; }
    .sidebar { background: #1a7a4a; min-height: 100vh; width: 220px; position: fixed; }
    .sidebar .brand { color: #fff; font-weight: 700; font-size: 1.1rem; padding: 1.5rem 1rem; }
    .sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: 8px; margin: 2px 8px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
    .main { margin-left: 220px; padding: 2rem; }
    .stat-card { background: #fff; border-radius: 12px; padding: 1.25rem; border: 1px solid #e0e0e0; }
    .btn-eco { background: #1a7a4a; color: #fff; border: none; }
    .btn-eco:hover { background: #155c38; color: #fff; }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="brand">♻️ Eco-Bridge</div>
    <nav class="nav flex-column px-2">
      <a href="?url=dashboard" class="nav-link active"><i class="bi bi-grid me-2"></i>Dashboard</a>
      <a href="?url=dashboard" class="nav-link" onclick="document.getElementById('postModal').style.display='flex'">
        <i class="bi bi-plus-circle me-2"></i>Daftarkan Limbah
      </a>
      <a href="?url=logout" class="nav-link mt-4"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
    <div class="px-3 mt-4">
      <div style="color:rgba(255,255,255,.6);font-size:.8rem;">Login sebagai</div>
      <div style="color:#fff;font-size:.9rem;font-weight:600;"><?= sanitize($_SESSION['name']) ?></div>
    </div>
  </div>

  <!-- Main -->
  <div class="main">
    <?php if ($success): ?>
      <div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><?= $error ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-0">Dashboard Mahasiswa</h4>
        <p class="text-muted small mb-0">Kelola limbah yang Anda daftarkan</p>
      </div>
      <button class="btn btn-eco fw-semibold" data-bs-toggle="modal" data-bs-target="#postModal">
        <i class="bi bi-plus-circle"></i> Daftarkan Limbah
      </button>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="stat-card text-center">
          <i class="bi bi-box-seam fs-3 text-success"></i>
          <div class="fw-bold fs-4"><?= $stats['total_items'] ?? 0 ?></div>
          <div class="text-muted small">Total Item</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card text-center">
          <i class="bi bi-clock fs-3 text-warning"></i>
          <div class="fw-bold fs-4"><?= $stats['pending'] ?? 0 ?></div>
          <div class="text-muted small">Menunggu Pickup</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card text-center">
          <i class="bi bi-check2-circle fs-3 text-primary"></i>
          <div class="fw-bold fs-4"><?= $stats['completed'] ?? 0 ?></div>
          <div class="text-muted small">Selesai</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card text-center">
          <i class="bi bi-recycle fs-3 text-success"></i>
          <div class="fw-bold fs-4"><?= number_format($stats['total_weight'] ?? 0, 1) ?></div>
          <div class="text-muted small">Total Berat (kg)</div>
        </div>
      </div>
    </div>

    <!-- Tabel item -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
      <div class="card-header bg-white border-0 pt-3"><h6 class="mb-0">Item Saya</h6></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Item</th><th>Kategori</th><th>Berat</th><th>Status</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php if (empty($myItems)): ?>
              <tr><td colspan="5" class="text-center text-muted py-4">Belum ada item. Daftarkan limbah pertama Anda!</td></tr>
            <?php else: ?>
              <?php foreach ($myItems as $it): ?>
              <tr>
                <td>
                  <div class="fw-semibold"><?= sanitize($it['title']) ?></div>
                  <div class="text-muted small"><?= sanitize($it['location']) ?></div>
                </td>
                <td><span class="badge bg-success-subtle text-success"><?= getCategoryLabel($it['category']) ?></span></td>
                <td><?= number_format($it['weight_kg'], 1) ?> kg</td>
                <td><?= renderStatusBadge($it['status']) ?></td>
                <td>
                  <?php if ($it['status'] === 'requested'): ?>
                    <span class="badge bg-warning text-dark">Ada Request!</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Request masuk dari UMKM -->
    <?php $pendingTrx = array_filter($myTrx, fn($t) => $t['status'] === 'pending'); ?>
    <?php if (!empty($pendingTrx)): ?>
    <div class="card border-warning border-0 shadow-sm" style="border-radius:12px;">
      <div class="card-header bg-warning bg-opacity-10 border-0 pt-3">
        <h6 class="mb-0 text-warning"><i class="bi bi-bell-fill"></i> Request Masuk dari UMKM</h6>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr><th>Item</th><th>UMKM</th><th>Tgl Pickup</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pendingTrx as $t): ?>
            <tr>
              <td class="fw-semibold"><?= sanitize($t['item_title']) ?></td>
              <td>
                <div><?= sanitize($t['umkm_name']) ?></div>
                <div class="text-muted small"><?= sanitize($t['umkm_institution']) ?></div>
              </td>
              <td><?= $t['pickup_date'] ?></td>
              <td>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                  <button name="action" value="accept" class="btn btn-success btn-sm">✓ Terima</button>
                  <button name="action" value="reject" class="btn btn-outline-danger btn-sm">✗ Tolak</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Modal post item -->
  <div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header border-0">
          <h5 class="modal-title">♻️ Daftarkan Limbah Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Item *</label>
                <input type="text" name="title" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Kategori *</label>
                <select name="category" class="form-select" required>
                  <option value="">-- Pilih --</option>
                  <option value="plastik">♻️ Plastik</option>
                  <option value="kertas">📄 Kertas</option>
                  <option value="logam">🔩 Logam</option>
                  <option value="kaca">🪟 Kaca</option>
                  <option value="elektronik">💻 Elektronik</option>
                  <option value="minyak_jelantah">🫙 Minyak Jelantah</option>
                  <option value="organik">🌿 Organik</option>
                  <option value="lainnya">📦 Lainnya</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">Berat (kg) *</label>
                <input type="number" name="weight_kg" class="form-control" step="0.1" min="0.1" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Lokasi *</label>
                <input type="text" name="location" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Foto</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-eco w-100 fw-semibold py-2">
                  <i class="bi bi-upload"></i> Daftarkan Item
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>