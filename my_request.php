<?php
$trxController = new TransactionController();
$trxController->confirmPickup();

$trx     = new Transaction();
$myTrx   = $trx->getByUmkm($_SESSION['user_id']);
$success = Session::getFlash('success');
$error   = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Request Saya | Eco-Bridge</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f0f7f0; }
    .sidebar { background: #1a7a4a; min-height: 100vh; width: 220px; position: fixed; }
    .sidebar .brand { color: #fff; font-weight: 700; font-size: 1.1rem; padding: 1.5rem 1rem; }
    .sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: 8px; margin: 2px 8px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
    .main { margin-left: 220px; padding: 2rem; }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="brand">♻️ Eco-Bridge</div>
    <nav class="nav flex-column px-2">
      <a href="?url=dashboard" class="nav-link"><i class="bi bi-search me-2"></i>Browse Limbah</a>
      <a href="?url=my_request" class="nav-link active"><i class="bi bi-list-check me-2"></i>Request Saya</a>
      <a href="?url=logout" class="nav-link mt-4"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </div>

  <div class="main">
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <h4 class="mb-4">Request Pickup Saya</h4>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Item</th><th>Kategori</th><th>Berat</th><th>Mahasiswa</th><th>Tgl Pickup</th><th>Status</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <?php if (empty($myTrx)): ?>
              <tr><td colspan="7" class="text-center text-muted py-4">Belum ada request.</td></tr>
            <?php else: ?>
              <?php foreach ($myTrx as $t): ?>
              <tr>
                <td class="fw-semibold"><?= sanitize($t['item_title']) ?></td>
                <td><?= getCategoryLabel($t['category']) ?></td>
                <td><?= number_format($t['weight_kg'], 1) ?> kg</td>
                <td>
                  <div><?= sanitize($t['mahasiswa_name']) ?></div>
                  <div class="text-muted small"><?= sanitize($t['mahasiswa_phone']) ?></div>
                </td>
                <td><?= $t['pickup_date'] ?></td>
                <td><?= renderStatusBadge($t['status']) ?></td>
                <td>
                  <?php if ($t['status'] === 'accepted'): ?>
                    <form method="POST">
                      <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                      <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check2-all"></i> Konfirmasi Diambil
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>