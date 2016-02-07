<?php namespace Zae\WPVulnerabilities\Tests;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Container\Container as ConcreteContainer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use PHPUnit_Framework_TestCase;
use Zae\WPVulnerabilities\ServiceProviders\AliasesServiceProvider;
use Zae\WPVulnerabilities\ServiceProviders\ConfigServiceProvider;

class TestCase extends PHPUnit_Framework_TestCase
{
	protected $container;

	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);

		$this->container = new ConcreteContainer;

		$this->bindings($this->container, [
			EventServiceProvider::class,
			FilesystemServiceProvider::class,
			PipelineServiceProvider::class,
			AliasesServiceProvider::class,
			ConfigServiceProvider::class,
		]);
	}

	/**
	 * @param Container $app
	 * @param array     $providers
	 */
	private function bindings(Container &$app, array $providers)
	{
		Collection::make($providers)
				  ->map(function ($provider) use ($app) {
					  return $app->make($provider, [$app]);
				  })
				  ->filter(function ($object) {
					  return is_a($object, ServiceProvider::class);
				  })
				  ->each(function (ServiceProvider $provider) {
					  $provider->register();
				  });
	}
}