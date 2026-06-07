<?php
session_start();

require_once 'Databasee.php';
require_once 'session.php';
require_once 'helpers.php';
require_once 'user.php';
require_once 'item.php';
require_once 'transaction.php';
require_once 'AuthController.php';
require_once 'ItemController.php';
require_once 'TransactionController.php';

$url = $_GET['url'] ?? 'dashboard';
$url = rtrim($url, '/');

$publicPages = ['login', 'register'];
if (!isset($_SESSION['user_id']) && !in_array($url, $publicPages)) {
    header('Location: ?url=login');
    exit;
}

switch ($url) {
    case 'login':      require 'login.php';         break;
    case 'register':   require 'register.php';      break;
    case 'dashboard':
        $role = $_SESSION['role'] ?? '';
        if ($role === 'mahasiswa')      require 'dashboard_mhs.php';
        elseif ($role === 'umkm')       require 'dashboard_umkm.php';
        elseif ($role === 'admin')      require 'dashboard_admin.php';
        else { header('Location: ?url=login'); exit; }
        break;
    case 'post_item':  require 'post_item.php';     break;
    case 'my_request': require 'my_request.php';    break;
    case 'logout':
        session_destroy();
        header('Location: ?url=login');
        exit;
    default:
        echo '<h2>404 - Halaman tidak ditemukan</h2>';
}