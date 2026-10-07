<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== PUBLIC ROUTES ====================
$routes->setAutoRoute(true);

// HOME & LANDING
$routes->get('/', 'LandingControllers::index');
$routes->get('about', 'LandingControllers::about');
$routes->get('contact', 'LandingControllers::contact');

// AUTHENTICATION
$routes->get('login', 'AuthControllers::login');
$routes->post('login', 'AuthControllers::attemptLogin');
$routes->get('register', 'AuthControllers::register');
$routes->post('register', 'AuthControllers::saveRegister');
$routes->get('logout', 'AuthControllers::logout');

// EMAIL VERIFICATION
$routes->get('verify-email/(:any)', 'AuthControllers::verifyEmail/$1');
$routes->post('resend-verification', 'AuthControllers::resendVerification');

// PUBLIC STOREFRONT & PRODUCTS
$routes->get('storefront', 'StoreControllers::index');
$routes->get('store', 'StoreControllers::index');
$routes->get('shop', 'StoreControllers::index');

$routes->get('products', 'ProductsControllers::index');
$routes->get('products/category/(:any)', 'ProductsControllers::category/$1');
$routes->get('products/search', 'ProductsControllers::search');
$routes->get('product-details/(:num)', 'ProductDetailsControllers::index/$1');

// CART
$routes->get('cart', 'CartControllers::index');
$routes->match(['get', 'post'], 'cart/add/(:num)', 'CartControllers::add/$1');
$routes->post('cart/update/(:num)', 'CartControllers::update/$1');
$routes->post('cart/remove/(:num)', 'CartControllers::remove/$1');
$routes->get('cart/clear', 'CartControllers::clear');
$routes->get('cart/count', 'CartControllers::getCartCount');
$routes->post('cart/save-selected', 'CartControllers::saveSelected');
$routes->match(['get', 'post'], 'cart/buy-now/(:num)', 'CartControllers::buyNow/$1');

// CUSTOMER PROFILE
$routes->get('profile', 'ProfileControllers::index');
$routes->post('profile/update', 'ProfileControllers::update');
$routes->post('profile/upload-picture', 'ProfileControllers::uploadProfilePicture');
$routes->post('profile/remove-picture', 'ProfileControllers::removeProfilePicture');
$routes->get('profile/picture/(:segment)', 'ProfileControllers::getProfilePicture/$1');

// CHECKOUT
$routes->get('checkout', 'CheckoutControllers::index');
$routes->post('checkout/process', 'CheckoutControllers::process');

// TRANSACTION HISTORY
$routes->get('transactions', 'HistoryControllers::index');
$routes->get('order-details/(:num)', 'HistoryControllers::details/$1');
$routes->post('order/cancel/(:num)', 'HistoryControllers::cancel/$1');
$routes->post('order/receive/(:num)', 'HistoryControllers::receive/$1');
$routes->post('order/pickup/(:num)', 'HistoryControllers::pickup/$1');

// ADMIN ROUTES
$routes->group('admin', function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'AdminControllers::dashboard');

    // Storefront management
    $routes->get('storefront', 'AdminControllers::storefront');
    $routes->post('storefront/update', 'AdminControllers::updateStorefront');

    // Inventory
    $routes->get('inventory', 'AdminControllers::inventory');
    $routes->get('inventory-report', 'AdminControllers::inventoryReport');

    // Product management
    $routes->get('add-product', 'AdminControllers::addProduct');
    $routes->post('product/save', 'AdminControllers::saveProduct');
    $routes->get('edit-product/(:num)', 'AdminControllers::editProduct/$1');
    $routes->post('product/update/(:num)', 'AdminControllers::updateProduct/$1');
    $routes->get('delete-product/(:num)', 'AdminControllers::deleteProduct/$1');

    // Reports
    $routes->get('reports', 'AdminControllers::reports');
    $routes->get('reports/sales', 'AdminControllers::salesReport');
    $routes->get('reports/products', 'AdminControllers::productsReport');

    // Orders
    $routes->get('orders', 'AdminControllers::orders');
    $routes->get('order-details/(:num)', 'AdminControllers::orderDetails/$1');
    $routes->post('orders/update-status/(:num)', 'AdminControllers::updateOrderStatus/$1');

    // Users
    $routes->get('users', 'AdminControllers::users');
    $routes->get('users/create', 'AdminControllers::addUser');
    $routes->post('users/store', 'AdminControllers::saveUser');
    $routes->get('users/edit/(:num)', 'AdminControllers::editUser/$1');
    $routes->post('users/update/(:num)', 'AdminControllers::updateUser/$1');
    $routes->get('users/delete/(:num)', 'AdminControllers::deleteUser/$1');

    // User profile pictures
    $routes->post('users/upload-picture/(:num)', 'AdminControllers::uploadProfilePicture/$1');
    $routes->post('users/remove-picture/(:num)', 'AdminControllers::removeProfilePicture/$1');

    // Verify user
    $routes->match(['get', 'post'], 'users/verify/(:num)', 'AdminControllers::verifyUser/$1');
});

// STATIC PAGES
$routes->get('faq', 'PagesControllers::faq');
$routes->get('shipping-policy', 'PagesControllers::shipping');
$routes->get('privacy-policy', 'PagesControllers::privacy');
$routes->get('terms', 'PagesControllers::terms');

// TEST ROUTES
$routes->get('test-email', 'TestEmailControllers::index');
$routes->get('test-welcome', 'TestEmailControllers::testWelcome');
$routes->get('test-config', 'TestEmailControllers::checkConfig');
$routes->get('check-env', 'TestEmailControllers::checkEnv');

// FALLBACK
$routes->set404Override(function ($message = null) {
    $data = [
        'message' => $message ?? 'The page you are looking for cannot be found.',
    ];

    return view('errors/html/error_404', $data);
});