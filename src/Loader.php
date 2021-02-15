<?php
/**
 * @noinspection PhpIncludeInspection
 */

namespace Dashifen\MUPluginLoader;

use DirectoryIterator;
use WP_Plugins_List_Table;
use Dashifen\WPHandler\Handlers\HandlerException;
use Dashifen\WPHandler\Handlers\Plugins\AbstractMustUsePluginHandler;

class Loader extends AbstractMustUsePluginHandler implements LoaderInterface
{
  private ?array $plugins = null;
  
  /**
   * loadPlugins
   *
   * Identifies and loads MU plugins that are located in sub-folders of the
   * wp-content/mu-plugins folder.  Allows WP Core to load any MU plugins that
   * are constructed in the typical way prescribed by WP.
   *
   * @return void
   */
  public function loadPlugins(): void
  {
    // based on the work of Luke Woodward in the lkwdwrd/wp-muplugin-loader
    // repo, this method identifies the plugins we're going to load and then
    // does so.  unlike the wp-muplugin-loader, this object does not (at this
    // time) use a transient to try and speed up loading.
    
    if (!function_exists('get_plugin_data')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    foreach ($this->getPlugins() as $plugin) {
      require_once $plugin;
    }
  }
  
  /**
   * getPlugins
   *
   * Returns an array of plugin files to be loaded.
   *
   * @return array
   */
  private function getPlugins(): array
  {
    if ($this->plugins !== null) {
      
      // we only want to do our filesystem search once per request.  if an MU
      // plugin somehow leaps onto the filesystem between the time when MU
      // plugins are loaded and when we display them on-screen in the table, we
      // don't want to accidentally claim that something was loaded that isn't.
      // so, we cache the plugins that we find in the loop below.  so, if we've
      // already done that loop, we just return the same list as before.
      
      return $this->plugins;
    }
    
    $this->plugins = [];
    foreach (new DirectoryIterator(WPMU_PLUGIN_DIR) as $muPlugin) {
      
      // as long as we're looking at a directory that's neither . nor .., we
      // want to see if we can find a WP plugin file in the folder.  if we do
      // find such a file, we'll add it to an array which we return below.
      // notice we also skip this plugin's folder because we know it's already
      // been loaded or we wouldn't be here!
      
      if (
        !$muPlugin->isDot()
        && $muPlugin->isDir()
        && $muPlugin->getFilename() !== 'mu-plugin-loader'
      ) {
        foreach (new DirectoryIterator($muPlugin->getPathname()) as $file) {
          
          // just like the outer loop, the DirectoryIterator here will grab
          // the relative folder links and other non-file based details.  so,
          // if this is a file, we get it's path, and if it's extension is php,
          // we see if it's a plugin.  if it is, we add it to the list.
          
          if ($file->isFile()) {
            $path = $file->getPathname();
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
              $pluginInfo = get_plugin_data($path);
              if (!empty($pluginInfo['Name'])) {
                $this->plugins[] = $path;
              }
            }
          }
        }
      }
    }
    
    return $this->plugins;
  }
  
  /**
   * initialize
   *
   * Uses the addAction and/or addFilter methods to attach protected methods of
   * this object to the WordPress ecosystem.
   *
   * @return void
   * @throws HandlerException
   */
  public function initialize(): void
  {
    if (!$this->isInitialized()) {
      $this->addAction('after_plugin_row_mu-plugin-loader.php', 'showLoadedPlugins');
    }
  }
  
  /**
   * showLoadedPlugins
   *
   * Adds the plugins we load to the MU plugins table of the WP Dashboard.
   *
   * @return void
   */
  protected function showLoadedPlugins(): void
  {
    $table = new WP_Plugins_List_Table();
    foreach ($this->getPlugins() as $plugin) {
      $pluginData = get_plugin_data($plugin, false);
      
      // to help identify the ones that we've loaded instead of any that WP
      // core might have loaded for us, we add a check mark as a prefix to the
      // plugin's name.
      
      $pluginData['Name'] = '✅&nbsp;&nbsp;' . $pluginData['Name'];
      $table->single_row([$plugin, $pluginData]);
    }
  }
}
