<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('manifest.webmanifest', 'Pwa::manifest');

$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('ingresar', 'Auth::login');
    $routes->post('ingresar', 'Auth::attemptLogin');
    $routes->get('registro', 'Auth::register');
    $routes->post('registro', 'Auth::attemptRegister');
});

$routes->get('salir', 'Auth::logout');

$routes->group('admin', ['filter' => 'admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->post('registro', 'Admin\Dashboard::saveRegistration');
    $routes->get('negocios', 'Admin\Businesses::index');
    $routes->get('negocios/nuevo', 'Admin\Businesses::createForm');
    $routes->post('negocios/nuevo', 'Admin\Businesses::store');
    $routes->get('negocios/(:num)', 'Admin\Businesses::detail/$1');
    $routes->post('negocios/(:num)/plan', 'Admin\Businesses::savePlan/$1');
    $routes->post('negocios/(:num)/info', 'Admin\Businesses::saveInfo/$1');
    $routes->post('negocios/(:num)/password', 'Admin\Businesses::resetPassword/$1');
    $routes->post('negocios/(:num)/suspender', 'Admin\Businesses::toggleSuspend/$1');
    $routes->post('negocios/(:num)/eliminar', 'Admin\Businesses::deleteBusiness/$1');
    $routes->get('cuenta', 'Admin\Account::index');
    $routes->post('cuenta', 'Admin\Account::save');
});

$routes->group('panel', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Panel\Dashboard::index');

    $routes->get('negocio', 'Panel\BusinessSettings::index');
    $routes->post('negocio', 'Panel\BusinessSettings::save');
    $routes->post('negocio/password', 'Panel\BusinessSettings::changePassword');

    $routes->get('asesores', 'Panel\Advisors::index');
    $routes->get('asesores/nuevo', 'Panel\Advisors::create');
    $routes->post('asesores', 'Panel\Advisors::store');
    $routes->get('asesores/(:num)/editar', 'Panel\Advisors::edit/$1');
    $routes->post('asesores/(:num)', 'Panel\Advisors::update/$1');
    $routes->post('asesores/(:num)/eliminar', 'Panel\Advisors::delete/$1');

    $routes->get('categorias', 'Panel\Categories::index');
    $routes->post('categorias', 'Panel\Categories::store');
    $routes->post('categorias/(:num)', 'Panel\Categories::update/$1');
    $routes->post('categorias/(:num)/eliminar', 'Panel\Categories::delete/$1');

    $routes->get('productos', 'Panel\Products::index');
    $routes->get('productos/nuevo', 'Panel\Products::create');
    $routes->post('productos', 'Panel\Products::store');
    $routes->get('productos/(:num)/editar', 'Panel\Products::edit/$1');
    $routes->post('productos/(:num)', 'Panel\Products::update/$1');
    $routes->post('productos/(:num)/eliminar', 'Panel\Products::delete/$1');
});

$routes->addPlaceholder('uuid', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');

$routes->get('c/(:uuid)', 'Quotes::show/$1');
$routes->post('c/(:segment)/(:segment)/cotizacion', 'Quotes::create/$1/$2');
$routes->get('c/(:segment)', 'Catalog::show/$1');
$routes->get('c/(:segment)/(:segment)', 'Catalog::advisor/$1/$2');
