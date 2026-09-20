<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default Route Settings
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('loginUser');
$routes->setAutoRoute(false);

// Auth
$routes->get('/', 'Auth::login');
$routes->match(['get', 'post'], 'login', 'Auth::loginUser');

$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerUser');

$routes->get('profile', 'Auth::profile');
$routes->get('logout', 'Auth::logout');

// Admin (Managing Users)
$routes->get('admin', 'Admin::index');
$routes->get('admin/edit/(:num)', 'Admin::edit/$1');
$routes->post('admin/update/(:num)', 'Admin::update/$1');
$routes->post('admin/updateStatus/(:num)', 'Admin::updateStatus/$1');
$routes->get('admin/delete/(:num)', 'Admin::delete/$1');

// Admin Profile (Logged-in Admin Personal Profile)
$routes->get('admin/profile', 'AdminProfile::index');
$routes->post('admin/profile/update', 'AdminProfile::update');
$routes->get('admin/profile/picture/(:any)', 'AdminProfile::picture/$1');

// Customer
$routes->get('order', 'Order::index');
$routes->post('order', 'Order::create');

$routes->get('/profile/pic/(:any)', 'Profile::picture/$1');
$routes->get('/profile/pic', 'Profile::picture');
$routes->post('profile/update', 'Profile::updateProfile');

// General
$routes->get('homepage', 'Home::index');