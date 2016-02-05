<?php namespace Zae\WPVulnerabilities\Providers\Themes;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Entities\Entity;
use Zae\WPVulnerabilities\Services\WordpressFileHeader;

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
	 * @var \Zae\WPVulnerabilities\Services\WordpressFileHeader
	 */
	private $header;

	/**
	 * Composer constructor.
	 *
	 * @param Filesystem                                          $filesystem
	 * @param Config                                              $config
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
		$composer_plugins = $this->findThemes();

		$plugins = array_merge($plugins, $composer_plugins);

		return $next($plugins);
	}

	/**
	 * @return mixed
	 */
	private function findThemes()
	{
		$directories = $this->filesystem->directories($this->config->get('themes.path'));

		return Collection::make($directories)->reduce(function(&$initial, $directory) {
			$plugin = $this->get_theme_date($directory . '/style.css');

			if (!empty($plugin['Name']) &&  !empty($plugin['Version'])) {
				$plugin['Name'] = basename($directory);
				$initial[] = $this->transformWPPluginToTheme($plugin);
			}

			return $initial;
		}, []);
	}

	/**
	 * @param $plugin_file
	 *
	 * @return array
	 */
	private function get_theme_date($plugin_file ) {

		$default_headers = array(
			'Name' => 'Theme Name',
			'Title' => 'Theme Name',
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
	private function transformWPPluginToTheme(array $plugin)
	{
		return new Entity($plugin['Name'], $plugin['Version'], $plugin['Title']);
	}
}

