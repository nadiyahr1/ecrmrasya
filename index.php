<?php

$controller = $_GET['controller'] ?? 'home';
$action     = $_GET['action'] ?? 'index';

switch ($controller) {

    case 'auth':
        require_once 'controllers/AuthController.php';
        $c = new AuthController();
        break;

    case 'keranjang':
        require_once 'controllers/KeranjangController.php';
        $c = new KeranjangController();
        break;

    case 'checkout':
        require_once 'controllers/CheckoutController.php';
        $c = new CheckoutController();
        break;

    case 'menu':
        require_once 'controllers/MenuController.php';
        $c = new MenuController();
        break;

    case 'fasilitas':
        require_once 'controllers/FasilitasController.php';
        $c = new FasilitasController();
        break;

    case 'pelanggan':
        require_once 'controllers/PelangganController.php';
        $c = new PelangganController();
        break;

    case 'home':
    default:
        require_once 'controllers/HomeController.php';
        $c = new HomeController();
        break;

    case 'admin':
        require_once 'controllers/AdminController.php';
        $c = new AdminController();
        break;

    case 'webhook':
        require_once 'controllers/WebhookController.php';
        $c = new WebhookController();
        $c->midtrans_handler();
        break;

    case 'laporan':
        require_once 'controllers/LaporanController.php';
        $c = new LaporanController();
        break;

    case 'owner':
        require_once 'controllers/OwnerController.php';
        $c = new OwnerController();
        break;
}

if (!method_exists($c, $action)) {
    die("Method tidak ditemukan");
}

$c->$action();
