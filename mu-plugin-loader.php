<?php

namespace Dashifen\WordPress\Plugins\MustUse;

use Dashifen\WPHandler\Handlers\HandlerException;
use Dashifen\WordPress\Plugins\MustUse\MUPluginLoader\Loader;

if (!class_exists(Loader::class)) {
  require_once 'vendor/autoload.php';
}

(function () {
  try {
    if (!defined('WP_INSTALLING') || !WP_INSTALLING) {
      $loader = new Loader();
      $loader->initialize();
      $loader->loadPlugins();
    }
  } catch (HandlerException $e) {
    Loader::catcher($e);
  }
})();
