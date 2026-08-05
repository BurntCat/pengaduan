<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------------------------------------------------
// Rute Publik - Form Pengaduan
// -------------------------------------------------------------
$routes->get('/', 'PengaduanController::index');
$routes->get('pengaduan', 'PengaduanController::index');
$routes->post('pengaduan/kirim', 'PengaduanController::kirim');
$routes->get('pengaduan/status/(:segment)', 'PengaduanController::status/$1');

// Rute Riwayat Langsung dalam Bentuk List (Hapus fungsi cari yang lama)
$routes->get('pengaduan/riwayat', 'PengaduanController::riwayatSemua'); 

// -------------------------------------------------------------
// Rute Admin - Pengaturan SLA
// -------------------------------------------------------------
$routes->group('admin', static function ($routes) {
    $routes->get('pengaduan', 'Admin\PengaduanAdminController::index');
    $routes->get('pengaduan/(:segment)', 'Admin\PengaduanAdminController::edit/$1');
    $routes->post('pengaduan/(:segment)/simpan', 'Admin\PengaduanAdminController::simpan/$1');
    $routes->post('pengaduan/update-sla', 'AdminController::updateSla');
    $routes->get('pengaduan/(:segment)/form-tolak', 'Admin\PengaduanAdminController::formTolak/$1');
    $routes->post('pengaduan/(:segment)/tolak', 'Admin\PengaduanAdminController::tolak/$1');
});