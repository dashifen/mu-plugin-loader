<?php

namespace Dashifen\Plugins\MustUse\MUPluginLoader;

use Dashifen\WPHandler\Handlers\Plugins\PluginHandlerInterface;

interface LoaderInterface extends PluginHandlerInterface
{
  /**
   * loadPlugins
   *
   * Identifies and loads MU plugins that are located in sub-folders of the
   * wp-content/mu-plugins folder.  Allows WP Core to load any MU plugins that
   * are constructed in the typical way prescribed by WP.
   *
   * @return void
   */
  public function loadPlugins(): void;
}
