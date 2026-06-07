<?php
$auth = new AuthController();
$auth->login();
$error   = Session::getFlash('error');
$success = Session::getFlash('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Eco-Bridge</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      background: #0a2e1a;
      display: flex;
      overflow: hidden;
    }

    /* ── Sisi kiri: ilustrasi bumi ── */
    .left-panel {
      flex: 1;
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      overflow: hidden;
    }

    /* Lingkaran bumi besar di background */
    .earth-bg {
      position: absolute;
      width: 520px;
      height: 520px;
      border-radius: 50%;
      background: radial-gradient(circle at 35% 40%, #2d6a4f, #1b4332 50%, #081c0e);
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      box-shadow: 0 0 80px rgba(52, 211, 153, 0.15), inset 0 0 60px rgba(0,0,0,0.4);
    }

    /* Daratan abstrak di bumi */
    .earth-bg::before {
      content: '';
      position: absolute;
      width: 160px;
      height: 120px;
      background: #40916c;
      border-radius: 60% 40% 70% 30% / 50% 60% 40% 50%;
      top: 25%;
      left: 20%;
      opacity: 0.7;
    }
    .earth-bg::after {
      content: '';
      position: absolute;
      width: 100px;
      height: 80px;
      background: #52b788;
      border-radius: 40% 60% 30% 70% / 60% 40% 60% 40%;
      bottom: 28%;
      right: 22%;
      opacity: 0.6;
    }

    /* Partikel daun melayang */
    .leaf {
      position: absolute;
      font-size: 1.4rem;
      animation: floatLeaf linear infinite;
      opacity: 0;
    }
    @keyframes floatLeaf {
      0%   { transform: translateY(100vh) rotate(0deg);   opacity: 0; }
      10%  { opacity: 0.8; }
      90%  { opacity: 0.6; }
      100% { transform: translateY(-120px) rotate(360deg); opacity: 0; }
    }

    /* Teks di panel kiri */
    .left-content {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #fff;
    }
    .left-content .globe-icon {
      font-size: 5rem;
      display: block;
      margin-bottom: 1.5rem;
      filter: drop-shadow(0 0 20px rgba(52,211,153,0.5));
      animation: pulse 3s ease-in-out infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50%       { transform: scale(1.05); }
    }
    .left-content h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #d8f3dc;
      line-height: 1.3;
      margin-bottom: 1rem;
    }
    .left-content h1 span {
      color: #52b788;
    }
    .left-content p {
      color: rgba(255,255,255,.65);
      font-size: .95rem;
      line-height: 1.7;
      max-width: 340px;
      margin: 0 auto 2rem;
    }

    /* Stats kecil di panel kiri */
    .stats-row {
      display: flex;
      gap: 1.5rem;
      justify-content: center;
    }
    .stat-item {
      text-align: center;
    }
    .stat-item .num {
      font-size: 1.4rem;
      font-weight: 700;
      color: #52b788;
    }
    .stat-item .lbl {
      font-size: .72rem;
      color: rgba(255,255,255,.5);
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    /* Divider vertikal */
    .divider {
      width: 1px;
      background: rgba(255,255,255,.08);
      margin: 2rem 0;
    }

    /* ── Sisi kanan: form login ── */
    .right-panel {
      width: 460px;
      background: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 3.5rem;
      position: relative;
    }

    /* Lengkungan dekoratif di kiri form */
    .right-panel::before {
      content: '';
      position: absolute;
      left: -30px;
      top: 50%;
      transform: translateY(-50%);
      width: 60px;
      height: 200px;
      background: #fff;
      border-radius: 0 50px 50px 0;
    }

    .form-wrap { width: 100%; max-width: 340px; }

    .brand-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #d8f3dc;
      color: #1b4332;
      font-size: .8rem;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      margin-bottom: 1.5rem;
      letter-spacing: .03em;
    }

    .form-wrap h2 {
      font-size: 1.6rem;
      font-weight: 700;
      color: #1b4332;
      margin-bottom: .4rem;
    }
    .form-wrap .subtitle {
      color: #6c757d;
      font-size: .88rem;
      margin-bottom: 2rem;
    }

    .form-label {
      font-size: .82rem;
      font-weight: 600;
      color: #2d6a4f;
      margin-bottom: .4rem;
    }

    .form-control {
      border: 1.5px solid #e0e0e0;
      border-radius: 10px;
      padding: .7rem 1rem;
      font-size: .9rem;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus {
      border-color: #52b788;
      box-shadow: 0 0 0 3px rgba(82,183,136,.15);
    }

    .input-icon {
      position: relative;
    }
    .input-icon i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #adb5bd;
      font-size: 1rem;
    }
    .input-icon .form-control {
      padding-left: 2.5rem;
    }

    .btn-login {
      background: linear-gradient(135deg, #2d6a4f, #52b788);
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: .8rem;
      font-size: .95rem;
      font-weight: 600;
      width: 100%;
      margin-top: .5rem;
      transition: transform .15s, box-shadow .15s;
      letter-spacing: .02em;
    }
    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(45,106,79,.35);
      color: #fff;
    }
    .btn-login:active { transform: translateY(0); }

    .divider-text {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 1.25rem 0;
      color: #adb5bd;
      font-size: .8rem;
    }
    .divider-text::before,
    .divider-text::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e9ecef;
    }

    .register-link {
      text-align: center;
      font-size: .85rem;
      color: #6c757d;
      margin-top: 1.25rem;
    }
    .register-link a {
      color: #2d6a4f;
      font-weight: 600;
      text-decoration: none;
    }
    .register-link a:hover { text-decoration: underline; }

    /* SDGs badge bawah */
    .sdg-badges {
      display: flex;
      gap: 8px;
      justify-content: center;
      margin-top: 2rem;
    }
    .sdg-badge {
      background: #f0faf4;
      border: 1px solid #b7e4c7;
      color: #2d6a4f;
      font-size: .7rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 20px;
    }

    /* Alert */
    .alert-eco {
      background: #fff3cd;
      border: 1px solid #ffc107;
      color: #664d03;
      border-radius: 10px;
      padding: .75rem 1rem;
      font-size: .85rem;
      margin-bottom: 1rem;
    }
    .alert-danger-eco {
      background: #fff5f5;
      border: 1px solid #f5c2c7;
      color: #842029;
      border-radius: 10px;
      padding: .75rem 1rem;
      font-size: .85rem;
      margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
      .left-panel { display: none; }
      .right-panel { width: 100%; padding: 2rem 1.5rem; }
      .right-panel::before { display: none; }
    }
  </style>
</head>
<body>

  <!-- Panel Kiri -->
  <div class="left-panel">
    <div class="earth-bg"></div>

    <!-- Daun melayang -->
    <span class="leaf" style="left:8%;animation-duration:7s;animation-delay:0s;">🍃</span>
    <span class="leaf" style="left:25%;animation-duration:9s;animation-delay:2s;">🌿</span>
    <span class="leaf" style="left:55%;animation-duration:6s;animation-delay:1s;">🍀</span>
    <span class="leaf" style="left:75%;animation-duration:8s;animation-delay:3s;">🍃</span>
    <span class="leaf" style="left:88%;animation-duration:10s;animation-delay:0.5s;">🌱</span>

    <div class="left-content">
      <span class="globe-icon">🌍</span>
      <h1>Bersama Jaga <span>Bumi</span><br>dari Kampus</h1>
      <p>
        Platform mahasiswa untuk mengelola limbah kampus dan
        menghubungkannya ke UMKM daur ulang demi
        <strong style="color:#52b788;">circular economy</strong> yang nyata.
      </p>

      <div class="stats-row">
        <div class="stat-item">
          <div class="num">500+</div>
          <div class="lbl">Mahasiswa</div>
        </div>
        <div class="stat-item">
          <div class="num">120+</div>
          <div class="lbl">UMKM Mitra</div>
        </div>
        <div class="stat-item">
          <div class="num">2.4T</div>
          <div class="lbl">Limbah Saved</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel Kanan -->
  <div class="right-panel">
    <div class="form-wrap">

      <div class="brand-badge">
        ♻️ ECO-BRIDGE
      </div>

      <h2>Selamat Datang!</h2>
      <p class="subtitle">Masuk dan mulai kontribusi untuk bumi yang lebih hijau 🌱</p>

      <?php if ($error): ?>
        <div class="alert-danger-eco">⚠️ <?= $error ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert-eco">✅ <?= $success ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Alamat Email</label>
          <div class="input-icon">
            <i class="bi bi-envelope"></i>
            <input type="email" name="email" class="form-control"
                   placeholder="email@kampus.ac.id" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-icon">
            <i class="bi bi-lock"></i>
            <input type="password" name="password" class="form-control"
                   placeholder="••••••••" required>
          </div>
        </div>
        <button type="submit" class="btn-login">
          🌍 Masuk Sekarang
        </button>
      </form>

      <div class="divider-text">atau</div>

      <div class="register-link">
        Belum punya akun?
        <a href="?url=register">Daftar sebagai Mahasiswa / UMKM</a>
      </div>

      <div class="sdg-badges">
        <span class="sdg-badge">🎯 SDG 12</span>
        <span class="sdg-badge">🌡️ SDG 13</span>
        <span class="sdg-badge">🤝 Pertamina Sobat Bumi</span>
      </div>

    </div>
  </div>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>