<?php
/**
 * Plugin Name: MU Loader
 * Description: A mu-plugin that loads other MU plugins.
 * Author URI: mailto:dashifen@dashifen.com
 * Author: David Dashifen Kees
 * Version: 1.0.0
 *
 * @noinspection PhpStatementHasEmptyBodyInspection
 * @noinspection PhpIncludeInspection
 */

use Dashifen\MUPluginLoader\Loader;
use Dashifen\WPHandler\Handlers\HandlerException;

if (file_exists($autoloader = dirname(ABSPATH) . '/deps/vendor/autoload.php'));
elseif (file_exists($autoloader = dirname(ABSPATH) . '/vendor/autoload.php'));
elseif (file_exists($autoloader = ABSPATH . 'vendor/autoload.php'));
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
