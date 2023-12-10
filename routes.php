<?php

include 'session.php';
require_once __DIR__.'/router.php';

// 1.0 Web Pages External No Login
// get('/', 'index.php');
get('/', 'app/views/signin.php');
get('/signin', 'app/views/signin.php');
get('/login', 'app/views/signin.php');
get('/forgetpwd', 'app/views/forgetpwd.php');
get('/signup', 'app/views/signup.php');

// 1.1 Web Pages Internal Logged-In
get('/home', 'app/views/home.php');
get('/signout', 'app/views/signout.php');
get('/dashboard', 'app/views/dashboard.php');
get('/admin/accounts', 'app/views/admin/accounts.php');
get('/admin/permissions', 'app/views/admin/permissions.php');
get('/admin/logs', 'app/views/admin/logs.php');
get('/cronjob/jobs', 'app/views/cronjob/jobs.php');
get('/cronjob/logs', 'app/views/cronjob/logs.php');

get('/appusers', 'app/views/appusers/appusers.php');
get('/profile', 'app/views/profile.php');
get('/topups', 'app/views/orders/topups.php');
get('/orders', 'app/views/orders/orders.php');
get('/products', 'app/views/products/products.php');

// 1.2 Object Dedicated Pages
get('/appuser/$appuser', 'app/views/appusers/user.php');
get('/products/$product', 'app/views/products/product.php');

// 1.3 Special Pages
get('/customer/$customer/account', 'app/views/customers/account.php');
get('/customer/$customer/invoice', 'app/views/customers/invoice.php');
get('/customer/$customer/statement', 'app/views/customers/statement.php');
get('/customer/$customer/report', 'app/views/customers/report.php');

// 1.4 Web Controllers AJAX
post('/controller', 'controller.php');
post('/modals','app/helpers/forms.php');
post('/ctrl/auth','app/controllers/auth.php');
post('/ctrl/admin','app/controllers/admin.php');
post('/ctrl/appusers','app/controllers/appusers.php');
post('/ctrl/orders','app/controllers/orders.php');

// 3.0 Mobile/Web Webview Plugin

// 4.0 Mobile/Web Native Restful API

// 4.0 Page Not Found
any('/404','app/views/404.php');