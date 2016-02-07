<?php namespace Zae\WPVulnerabilities\Providers\Plugins;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Zae\WordpressFileHeader\WordpressFileHeader;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Entities\Entity;

/**
 * Class Providers
 *
 * @package Zae\WPVulnerabilities\Commands
 */
class Files
{
	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var WordpressFileHeader
	 */
	private $header;

	/**
	 * Composer constructor.
	 *
	 * @param Filesystem          $filesystem
	 * @param Config              $config
	 * @param WordpressFileHeader $header
	 */
	public function __construct(Filesystem $filesystem, Config $config, WordpressFileHeader $header)
	{
		$this->filesystem = $filesystem;
		$this->config = $config;
		$this->header = $header;
	}

	/**
	 * @param array   $plugins
	 * @param Closure $next
	 *
	 * @return mixed
	 */
	public function handle(array $plugins, Closure $next)
	{
		$composer_plugins = $this->findPlugins();

		$plugins = array_merge($plugins, $composer_plugins);

		return $next($plugins);
	}

	/**
	 * @return mixed
	 */
	private function findPlugins()
	{
		$directories = $this->filesystem->directories($this->config->get('plugins.path'));

		return Collection::make($directories)->reduce(function(&$initial, $directory) {
			$files = $this->filesystem->glob($directory . '/*.php');

			foreach ($files as $file) {
				$plugin = $this->get_plugin_data($file);

				if (!empty($plugin['Title']) &&  !empty($plugin['Version'])) {
					$plugin['Name'] = basename($directory);
					$initial[] = $this->transformWPPluginToPlugin($plugin);
				}
			}

			return $initial;
		}, []);
	}

	/**
	 * @param $plugin_file
	 *
	 * @return array
	 */
	private function get_plugin_data($plugin_file ) {

		$default_headers = array(
			'Name' => 'Plugin Name',
			'Title' => 'Plugin Name',
			'Version' => 'Version',
		);

		$plugin_data = $this->header->get_file_data( $plugin_file, $default_headers );

		return $plugin_data;
	}

	/**
	 * @param array $plugin
	 *
	 * @return Entity
	 */
	private function transformWPPluginToPlugin(array $plugin)
	{
		return new Entity($plugin['Name'], $plugin['Version'], $plugin['Title']);
	}
}

