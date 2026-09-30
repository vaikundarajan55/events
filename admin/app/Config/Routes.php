<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Root -> dashboard (auth filter will send guests to /login)
$routes->get('/', static fn () => redirect()->to('/dashboard'));

// ---------- Guest only ----------
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attempt');
});

// ---------- Admin (login required) ----------
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('logout', 'Auth::logout');

    $routes->get('dashboard', 'Dashboard::index');

    // Gallery
    $routes->get('gallery', 'Gallery::index');
    $routes->get('gallery/create', 'Gallery::create');
    $routes->post('gallery/store', 'Gallery::store');
    $routes->get('gallery/edit/(:num)', 'Gallery::edit/$1');
    $routes->post('gallery/update/(:num)', 'Gallery::update/$1');
    $routes->post('gallery/toggle/(:num)', 'Gallery::toggle/$1');
    $routes->post('gallery/delete/(:num)', 'Gallery::delete/$1');

    // Careers (job openings)
    $routes->get('careers', 'Careers::index');
    $routes->get('careers/create', 'Careers::create');
    $routes->post('careers/store', 'Careers::store');
    $routes->get('careers/edit/(:num)', 'Careers::edit/$1');
    $routes->post('careers/update/(:num)', 'Careers::update/$1');
    $routes->post('careers/toggle/(:num)', 'Careers::toggle/$1');
    $routes->post('careers/delete/(:num)', 'Careers::delete/$1');

    // Career responses (applications)
    $routes->get('responses', 'Responses::index');
    $routes->get('responses/export', 'Responses::export');
    $routes->get('responses/view/(:num)', 'Responses::show/$1');
    $routes->get('responses/resume/(:num)', 'Responses::resume/$1');
    $routes->post('responses/status/(:num)', 'Responses::status/$1');
    $routes->post('responses/delete/(:num)', 'Responses::delete/$1');
});
