<?php

// CORE
require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/registry.php';
require_once 'core/loader.php';
require_once 'core/db.php';
require_once 'core/db/mysqli.php';
// MODULES
require_once 'env.php';
require_once 'route.php';
require_once 'includes/functions.php';
require_once 'lib/sdek_api.php';
$registry = new Registry();
// Loader
$loader = new Loader($registry);
$registry->set('load', $loader); 
// Database
$db = new Database('MySQLi', DB_HOST, DB_LOGIN, DB_PASS, DB_NAME);
$registry->set('db', $db);
// Start route
Route::start($registry);
