<?php
/**
 * @noinspection PhpStatementHasEmptyBodyInspection
 * @noinspection PhpIncludeInspection
 */

use Dashifen\MUPluginLoader\Loader;
use Dashifen\WPHandler\Handlers\HandlerException;

if (file_exists($autoloader = ABSPATH . 'wp-content/vendor/autoload.php'));
else $autoloader = 'vendor/autoload.php';
require_once $autoloader;

(function () {
  try {
    if (!defined('WP_INSTALLING') || !WP_INSTALLING) {
      $loader = new Loader();
      $loader->initialize();
      $loader->loadPlugins();
    }
  } catch (HandlerException $e) {
    
    // since this is a MU plugin, there's not much we can do other than
    // print the exception's message on-screen and die.  luckily, the
    // likelihood of an exception in this on is slim to none.
    
    wp_die($e->getMessage());
  }
})();
