<?php
$trxController = new TransactionController();
$trxController->requestPickup();

$item     = new Item();
$trx      = new Transaction();
$category = $_GET['category'] ?? null;
$items    = $item->getAvailableItems($category);
$success  = Session::getFlash('success');
$error    = Session::getFlash('error');

$categories = [
    ['key' => 'plastik',          'label' => '♻️ Plastik'],
    ['key' => 'kertas',           'label' => '📄 Kertas'],
    ['key' => 'logam',            'label' => '🔩 Logam'],
    ['key' => 'kaca',             'label' => '🪟 Kaca'],
    ['key' => 'elektronik',       'label' => '💻 Elektronik'],
    ['key' => 'minyak_jelantah',  'label' => '🫙 Minyak Jelantah'],
    ['key' => 'organik',          'label' => '🌿 Organik'],
    ['key' => 'lainnya',          'label' => '📦 Lainnya'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard UMKM | Eco-Bridge</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f0f7f0; }
    .sidebar { background: #1a7a4a; min-height: 100vh; width: 220px; position: fixed; }
    .sidebar .brand { color: #fff; font-weight: 700; font-size: 1.1rem; padding: 1.5rem 1rem; }
    .sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: 8px; margin: 2px 8px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
    .main { margin-left: 220px; padding: 2rem; }
    .btn-eco { background: #1a7a4a; color: #fff; border: none; }
    .btn-eco:hover { background: #155c38; color: #fff; }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="brand">♻️ Eco-Bridge</div>
    <nav class="nav flex-column px-2">
      <a href="?url=dashboard" class="nav-link active"><i class="bi bi-search me-2"></i>Browse Limbah</a>
      <a href="?url=my_request" class="nav-link"><i class="bi bi-list-check me-2"></i>Request Saya</a>
      <a href="?url=logout" class="nav-link mt-4"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
    <div class="px-3 mt-4">
      <div style="color:rgba(255,255,255,.6);font-size:.8rem;">Login sebagai</div>
      <div style="color:#fff;font-size:.9rem;font-weight:600;"><?= sanitize($_SESSION['name']) ?></div>
    </div>
  </div>

  <div class="main">
    <?php if ($success): ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?= $success ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?= $error ?>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-0">Cari Bahan Baku Daur Ulang</h4>
        <p class="text-muted small mb-0">Browse dan ajukan request pickup dari mahasiswa</p>
      </div>
    </div>

    <!-- Filter kategori -->
    <div class="d-flex gap-2 flex-wrap mb-4">
      <a href="?url=dashboard" class="btn btn-sm <?= !$category ? 'btn-success' : 'btn-outline-secondary' ?>">Semua</a>
      <?php foreach ($categories as $cat): ?>
        <a href="?url=dashboard&category=<?= $cat['key'] ?>"
           class="btn btn-sm <?= $category === $cat['key'] ? 'btn-success' : 'btn-outline-secondary' ?>">
          <?= $cat['label'] ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Grid items -->
    <?php if (empty($items)): ?>
      <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="mt-2 text-muted">Belum ada item tersedia.</p>
      </div>
    <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
      <?php foreach ($items as $it): ?>
      <div class="col">
        <div class="card h-100 border-0 shadow-sm" style="border-radius:12px;">
          <div class="d-flex align-items-center justify-content-center bg-light"
               style="height:140px;border-radius:12px 12px 0 0;font-size:3rem;">
            <?php
              $emojis = [
                'plastik'        => '♻️',
                'kertas'         => '📄',
                'logam'          => '🔩',
                'kaca'           => '🪟',
                'elektronik'     => '💻',
                'minyak_jelantah'=> '🫙',
                'organik'        => '🌿',
                'lainnya'        => '📦'
              ];
              echo $emojis[$it['category']] ?? '📦';
            ?>
          </div>
          <div class="card-body">
            <span class="badge bg-success-subtle text-success mb-2">
              <?= getCategoryLabel($it['category']) ?>
            </span>
            <h6 class="card-title fw-semibold"><?= sanitize($it['title']) ?></h6>
            <p class="text-muted small"><?= sanitize(substr($it['description'] ?? '', 0, 80)) ?>...</p>
            <div class="d-flex gap-3 text-muted small mb-3">
              <span><i class="bi bi-box-seam"></i> <?= number_format($it['weight_kg'], 1) ?> kg</span>
              <span><i class="bi bi-geo-alt"></i> <?= sanitize($it['location']) ?></span>
            </div>
            <div class="d-flex align-items-center gap-2 border-top pt-2">
              <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold"
                   style="width:30px;height:30px;font-size:.75rem;">
                <?= strtoupper(substr($it['mahasiswa_name'], 0, 2)) ?>
              </div>
              <div>
                <div class="fw-semibold" style="font-size:.8rem;"><?= sanitize($it['mahasiswa_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem;"><?= sanitize($it['institution']) ?></div>
              </div>
            </div>
          </div>
          <div class="card-footer bg-transparent border-0 pb-3 px-3">
            <button class="btn btn-eco w-100 btn-sm fw-semibold"
                    onclick="openModal(<?= $it['id'] ?>, '<?= sanitize($it['title']) ?>')">
              <i class="bi bi-truck"></i> Request Pickup
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Modal request -->
  <div class="modal fade" id="reqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header border-0">
          <h5 class="modal-title"><i class="bi bi-truck text-success"></i> Request Pickup</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small">Item: <strong id="modalTitle"></strong></p>
          <form method="POST">
            <input type="hidden" name="item_id" id="modalItemId">
            <div class="mb-3">
              <label class="form-label fw-semibold">Tanggal Pickup</label>
              <input type="date" name="pickup_date" class="form-control"
                     min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Catatan</label>
              <textarea name="notes" class="form-control" rows="2"
                        placeholder="Jam kedatangan, dll."></textarea>
            </div>
            <button type="submit" class="btn btn-eco w-100 fw-semibold">Kirim Request</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openModal(id, title) {
      document.getElementById('modalItemId').value = id;
      document.getElementById('modalTitle').textContent = title;
      new bootstrap.Modal(document.getElementById('reqModal')).show();
    }
  </script>
</body>
</html>