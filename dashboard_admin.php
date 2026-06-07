<?php
$item      = new Item();
$user      = new User();
$trx       = new Transaction();
$catStats  = $item->getStatsByCategory();
$monthly   = $item->getMonthlySummary();
$totalUsers = $user->countAll();
$totalTrx   = $trx->countAll();
$totalItems = $item->countAll();
$totalSaved = $item->totalWeightSaved();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | Eco-Bridge</title>
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
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="brand">♻️ Eco-Bridge</div>
    <nav class="nav flex-column px-2">
      <a href="?url=dashboard" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="?url=logout" class="nav-link mt-4"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </div>

  <div class="main">
    <h4 class="mb-4">Dashboard Admin</h4>

    <!-- KPI -->
    <div class="row g-3 mb-4">
      <?php
      $kpis = [
        ['Item Terdaftar',     $totalItems,                         'box-seam',     '#1a7a4a'],
        ['Total Terselamatkan', number_format($totalSaved,1).' kg', 'recycle',      '#10b981'],
        ['Pengguna',           $totalUsers,                         'people',       '#3b82f6'],
        ['Transaksi Selesai',  $totalTrx,                           'check-circle', '#f59e0b'],
      ];
      foreach ($kpis as [$label, $val, $icon, $color]): ?>
      <div class="col-6 col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:46px;height:46px;background:<?= $color ?>22;flex-shrink:0;">
            <i class="bi bi-<?= $icon ?> fs-4" style="color:<?= $color ?>"></i>
          </div>
          <div>
            <div class="fw-bold fs-5"><?= $val ?></div>
            <div class="text-muted small"><?= $label ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
      <div class="col-md-5">
        <div class="card border-0 shadow-sm p-3" style="border-radius:12px;">
          <h6 class="mb-3">Komposisi per Kategori</h6>
          <canvas id="pieChart" height="220"></canvas>
        </div>
      </div>
      <div class="col-md-7">
        <div class="card border-0 shadow-sm p-3" style="border-radius:12px;">
          <h6 class="mb-3">Tren Limbah Terselamatkan (12 Bulan)</h6>
          <canvas id="lineChart" height="220"></canvas>
        </div>
      </div>
    </div>

    <!-- Tabel kategori -->
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
      <div class="card-header bg-white border-0 pt-3"><h6 class="mb-0">Ringkasan per Kategori</h6></div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr><th>Kategori</th><th>Total Item</th><th>Total Berat</th><th>Terselamatkan</th><th>% Saved</th></tr>
          </thead>
          <tbody>
            <?php foreach ($catStats as $cs):
              $pct = $cs['total_weight'] > 0 ? round($cs['weight_saved'] / $cs['total_weight'] * 100) : 0;
            ?>
            <tr>
              <td class="fw-semibold"><?= getCategoryLabel($cs['category']) ?></td>
              <td><?= $cs['total_items'] ?></td>
              <td><?= number_format($cs['total_weight'], 1) ?> kg</td>
              <td class="text-success fw-semibold"><?= number_format($cs['weight_saved'], 1) ?> kg</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px;">
                    <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                  </div>
                  <span class="small"><?= $pct ?>%</span>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
  (function() {
    const colors = ['#1a7a4a','#34d399','#f59e0b','#3b82f6','#ec4899','#8b5cf6','#06b6d4','#f97316'];
    const pieLabels  = <?= json_encode(array_map(fn($c) => getCategoryLabel($c['category']), $catStats)) ?>;
    const pieData    = <?= json_encode(array_column($catStats, 'total_weight')) ?>;
    const lineLabels = <?= json_encode(array_column($monthly, 'month')) ?>;
    const lineData   = <?= json_encode(array_column($monthly, 'total_weight')) ?>;

    new Chart(document.getElementById('pieChart'), {
      type: 'doughnut',
      data: { labels: pieLabels, datasets: [{ data: pieData, backgroundColor: colors, borderWidth: 2 }] },
      options: { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } }
    });

    new Chart(document.getElementById('lineChart'), {
      type: 'line',
      data: {
        labels: lineLabels,
        datasets: [{
          label: 'Berat terselamatkan (kg)',
          data: lineData,
          borderColor: '#1a7a4a',
          backgroundColor: '#1a7a4a22',
          tension: 0.4, fill: true, pointRadius: 4
        }]
      },
      options: {
        scales: { y: { beginAtZero: true }, x: {} },
        plugins: { legend: { display: false } }
      }
    });
  })();
  </script>
</body>
</html>