<?php namespace Zae\WPVulnerabilities\ServiceProviders;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Providers\Plugins\Files;
use Zae\WPVulnerabilities\Providers\General\FilterNotVulnerable;
use Zae\WPVulnerabilities\Providers\Plugins\VulnDB;
use Zae\WPVulnerabilities\Providers\Themes\Files as ThemeFiles;
use Zae\WPVulnerabilities\Providers\Themes\VulnDB as ThemeVulnDB;

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
		'http' => [
			'concurrency' => 5
		],
		'plugins' => [
			'providers' => [
				FilterNotVulnerable::class,
				Files::class,
				VulnDB::class
			],
		],
		'themes' => [
			'providers' => [
				FilterNotVulnerable::class,
				ThemeFiles::class,
				ThemeVulnDB::class
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
			$config1 = new Config(Arr::merge($this->defaultConfig, [
				'basepath' => getcwd(),
				'plugins' => [
					'path' => getcwd() . '/wp-content/plugins'
				],
				'themes' => [
					'path' => getcwd() . '/wp-content/themes'
				]
			], $config));

			return $config1;
		});
	}
}