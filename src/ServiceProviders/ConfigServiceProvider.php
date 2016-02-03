<?php namespace Zae\WPVulnerabilities\ServiceProviders;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Providers\Plugins\Files;
use Zae\WPVulnerabilities\Providers\Plugins\VulnDB;

/**
 * Class ConfigServiceProvider
 *
 * @package Zae\WPVulnerabilities\ServiceProviders
 */
class ConfigServiceProvider extends ServiceProvider
{
	/**
	 * @var array
	 */
	private $defaultConfig = [
		'plugins' => [
			'providers' => [
				Files::class,
				VulnDB::class
			],
		]
	];

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->readConfig();
	}

	/**
	 *
	 */
	private function readConfig()
	{
		$config = [];

		try {
			$config = Yaml::parse($this->app['files']->get('wp_scan.yml'));
		} catch (\Exception $e) {}

		$this->app->singleton(Config::class, function () use ($config) {
			return new Config(array_merge($this->defaultConfig, [
				'basepath' => getcwd(),
				'plugins' => [
					'path' => getcwd() . '/wp-content/plugins'
				]
			], $config));
		});
	}
}