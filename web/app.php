<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'app/init_autoloader.php';

$locator = new Wj\Framework\Config\ConfigLocator();
$config = $locator->locate('app/config/config');

// Run the application!
Zend\Mvc\Application::init($config)->run();
