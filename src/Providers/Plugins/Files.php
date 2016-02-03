<?php namespace Zae\WPVulnerabilities\Providers\Plugins;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Zae\WPVulnerabilities\Config;

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
	 * Composer constructor.
	 *
	 * @param Filesystem $filesystem
	 * @param Config     $config
	 */
	public function __construct(Filesystem $filesystem, Config $config)
	{
		$this->filesystem = $filesystem;
		$this->config = $config;
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
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path',
		);

		$plugin_data = $this->get_file_data( $plugin_file, $default_headers );

		$plugin_data['Title']      = $plugin_data['Name'];
		$plugin_data['AuthorName'] = $plugin_data['Author'];

		return $plugin_data;
	}

	/**
	 * @param $file
	 * @param $default_headers
	 *
	 * @return array
	 */
	private function get_file_data($file, $default_headers ) {
		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );

		$all_headers = [];

		foreach ( $default_headers as $field => $regex ) {
			if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
				$all_headers[$field] = $this->_cleanup_header_comment($match[1]);
			} else {
				$all_headers[$field] = '';
			}
		}

		return $all_headers;
	}

	/**
	 * @param $str
	 *
	 * @return string
	 */
	private function _cleanup_header_comment($str ) {
		return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
	}

	/**
	 * @param array $plugin
	 *
	 * @return \Zae\WPVulnerabilities\Plugin
	 */
	private function transformWPPluginToPlugin(array $plugin)
	{
		return new \Zae\WPVulnerabilities\Plugin($plugin['Name'], $plugin['Version'], $plugin['Title']);
	}
}