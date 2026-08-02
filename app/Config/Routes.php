<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Home (Landing Page)
$routes->get('/', 'Customer\Dashboard::index');

// Katalog Produk (Public)
$routes->get('catalog', 'Customer\Dashboard::catalog');

// Auth Routes
$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth\Login::index');
    $routes->post('login/process', 'Auth\Login::process');
    $routes->get('register', 'Auth\Register::index');
    $routes->post('register/process', 'Auth\Register::process');
    $routes->get('logout', 'Auth\Logout::index');
});

// Admin Routes
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');

    // Categories
    $routes->get('categories', 'Admin\Category::index');
    $routes->post('categories/store', 'Admin\Category::store');
    $routes->post('categories/update/(:num)', 'Admin\Category::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\Category::delete/$1');

    // Products
    $routes->get('products', 'Admin\Product::index');
    $routes->get('products/create', 'Admin\Product::create');
    $routes->post('products/store', 'Admin\Product::store');
    $routes->get('products/edit/(:num)', 'Admin\Product::edit/$1');
    $routes->post('products/update/(:num)', 'Admin\Product::update/$1');
    $routes->post('products/delete/(:num)', 'Admin\Product::delete/$1');

    // Customers
    $routes->get('customers', 'Admin\Customer::index');
    $routes->get('customers/(:num)', 'Admin\Customer::detail/$1');
    $routes->post('customers/update/(:num)', 'Admin\Customer::update/$1');

    // Bank Accounts
    $routes->get('banks', 'Admin\Bank::index');
    $routes->post('banks/store', 'Admin\Bank::store');
    $routes->post('banks/update/(:num)', 'Admin\Bank::update/$1');
    $routes->post('banks/delete/(:num)', 'Admin\Bank::delete/$1');

    // Transactions
    $routes->get('transactions', 'Admin\Transaction::index');
    $routes->get('transactions/create', 'Admin\Transaction::create');
    $routes->post('transactions/store', 'Admin\Transaction::store');
    $routes->get('transactions/detail/(:num)', 'Admin\Transaction::detail/$1');

    // Payment Verification
    $routes->get('payments', 'Admin\Payment::index');
    $routes->get('payments/detail/(:num)', 'Admin\Payment::detail/$1');
    $routes->post('payments/verify/(:num)', 'Admin\Payment::verify/$1');
    $routes->post('payments/reject/(:num)', 'Admin\Payment::reject/$1');

    // Promotions
    $routes->get('promotions', 'Admin\Promotion::index');
    $routes->get('promotions/create', 'Admin\Promotion::create');
    $routes->post('promotions/store', 'Admin\Promotion::store');
    $routes->get('promotions/edit/(:num)', 'Admin\Promotion::edit/$1');
    $routes->post('promotions/update/(:num)', 'Admin\Promotion::update/$1');

    // Notifications
    $routes->get('notifications', 'Admin\Notification::index');
    $routes->post('notifications/send', 'Admin\Notification::send');
    $routes->post('notifications/broadcast', 'Admin\Notification::broadcast');

    // Reports
    $routes->get('report', 'Admin\Report::index');
    $routes->get('report/sales', 'Admin\Report::sales');
    $routes->get('report/customer', 'Admin\Report::customer');
    $routes->get('report/loyalty', 'Admin\Report::loyalty');
    $routes->get('report/export/(:any)', 'Admin\Report::export/$1');
});

// Customer Routes
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Alamat Pengiriman
    $routes->get('address', 'Customer\Address::index');
    $routes->post('address/store', 'Customer\Address::store');
    $routes->post('address/update/(:num)', 'Customer\Address::update/$1');
    $routes->post('address/delete/(:num)', 'Customer\Address::delete/$1');
    $routes->post('address/set-default/(:num)', 'Customer\Address::setDefault/$1');

    // Detail Produk
    $routes->get('product/(:num)', 'Customer\Product::detail/$1');

    // Cart
    $routes->get('cart', 'Customer\Cart::index');
    $routes->post('cart/add', 'Customer\Cart::add');
    $routes->post('cart/update', 'Customer\Cart::update');
    $routes->post('cart/remove', 'Customer\Cart::remove');

    // Checkout
    $routes->get('checkout', 'Customer\Checkout::index');
    $routes->post('checkout/process', 'Customer\Checkout::process');
    $routes->post('checkout/upload-proof/(:num)', 'Customer\Checkout::uploadProof/$1');

    // Transactions
    $routes->get('transactions', 'Customer\Transaction::index');
    $routes->get('transactions/(:num)', 'Customer\Transaction::detail/$1');

    // Loyalitas
    $routes->get('loyalty', 'Customer\Loyalty::index');

    // Profil
    $routes->get('profile', 'Customer\Profile::index');
    $routes->post('profile/update', 'Customer\Profile::update');

    // Wishlist
    $routes->get('wishlist', 'Customer\Wishlist::index');
    $routes->post('wishlist/toggle/(:num)', 'Customer\Wishlist::toggle/$1');
    $routes->post('wishlist/remove/(:num)', 'Customer\Wishlist::remove/$1');

    // Review
    $routes->post('review/add', 'Customer\Review::add');

    // Notifications (API)
    $routes->get('api/notifications', 'Api\Notification::index');
    $routes->post('api/notifications/read/(:num)', 'Api\Notification::read/$1');
    $routes->post('api/notifications/read-all', 'Api\Notification::readAll');
});
