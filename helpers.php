<?php
function getCategoryLabel(string $key): string {
    return [
        'plastik'          => '♻️ Plastik',
        'kertas'           => '📄 Kertas',
        'logam'            => '🔩 Logam',
        'kaca'             => '🪟 Kaca',
        'elektronik'       => '💻 Elektronik',
        'minyak_jelantah'  => '🫙 Minyak Jelantah',
        'organik'          => '🌿 Organik',
        'lainnya'          => '📦 Lainnya',
    ][$key] ?? $key;
}

function renderStatusBadge(string $status): string {
    $map = [
        'available'  => ['Tersedia',      'success'],
        'requested'  => ['Diminta UMKM',  'warning'],
        'picked_up'  => ['Sudah Diambil', 'primary'],
        'completed'  => ['Selesai',       'secondary'],
        'cancelled'  => ['Dibatalkan',    'danger'],
    ];
    [$label, $cls] = $map[$status] ?? [$status, 'light'];
    return "<span class='badge bg-{$cls}'>{$label}</span>";
}

function redirect(string $url): void {
    header("Location: ?url={$url}");
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}