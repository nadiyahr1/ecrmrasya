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
        
    case 'home':
    default:
        require_once 'controllers/HomeController.php';
        $c = new HomeController();
        break;
}

if (!method_exists($c, $action)) {
    die("Method tidak ditemukan");
}

$c->$action();
