<?php namespace Zae\WPVulnerabilities\Providers\Wordpress;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
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
	 * @var Config
	 */
	private $config;

	/**
	 * Composer constructor.
	 *
	 * @param Config              $config
	 */
	public function __construct(Config $config)
	{
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

