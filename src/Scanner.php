<?php namespace Zae\WPVulnerabilities;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Console\Application;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Zae\WPVulnerabilities\ServiceProviders\AliasesServiceProvider;
use Zae\WPVulnerabilities\ServiceProviders\ConfigServiceProvider;

/**
 * Class Scanner
 *
 * @package Zae\WPVulnerabilities
 */
class Scanner extends Application
{
	/**
	 * The version of this plugin!
	 */
	const VERSION = '1.0';

	/**
	 * Scanner constructor.
	 *
	 * @param Container|null  $container
	 * @param Dispatcher|null $events
	 */
	public function __construct(Container $container = null, Dispatcher $events = null)
	{
		$container = $container ?: new Container;

		$this->bindings($container, [
			EventServiceProvider::class,
			FilesystemServiceProvider::class,
			PipelineServiceProvider::class,
			AliasesServiceProvider::class,
			ConfigServiceProvider::class,
		]);

		$events = $events ?: $container->make('events');

		parent::__construct($container, $events, static::VERSION);
		$this->setName('Wordpress Vulnerabilities Scanner');
	}

	/**
	 * @param Container $app
	 * @param array     $providers
	 */
	private function bindings(Container &$app, array $providers)
	{
		Collection::make($providers)
			->map(function($provider) use ($app) {
				return $app->make($provider, [$app]);
			})
			->filter(function($object){
				return is_a($object, ServiceProvider::class);
			})
			->each(function(ServiceProvider $provider) {
				$provider->register();
			});
	}
}