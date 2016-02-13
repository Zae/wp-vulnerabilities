<?php namespace Zae\WPVulnerabilities\Providers\Wordpress;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use Illuminate\Filesystem\Filesystem;
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
		$composer_plugins = [$this->findWordpress()];

		$plugins = array_merge($plugins, $composer_plugins);

		return $next($plugins);
	}

	/**
	 * @return mixed
	 */
	private function findWordpress()
	{
		return $this->get_wordpress_version($this->config->get('wordpress.path') . '/wp-includes/version.php');
	}

	/**
	 * @param $plugin_file
	 *
	 * @return array
	 */
	private function get_wordpress_version($plugin_file )
	{
		global $wp_version;

		ob_start();
		include $plugin_file;
		ob_end_clean();

		return new Entity('wordpress', $wp_version, 'Wordpress');
	}
}

