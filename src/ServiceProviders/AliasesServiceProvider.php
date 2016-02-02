<?php namespace Zae\WPVulnerabilities\ServiceProviders;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;
use Illuminate\Pipeline\Pipeline as PipelineConcrete;
use Illuminate\Support\ServiceProvider;

/**
 * Class AliasesServiceProvider
 *
 * @package Zae\WPVulnerabilities\ServiceProviders
 */
class AliasesServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerContainerAlias();
		$this->registerFilesystemAlias();
		$this->registerPipelineAlias();
	}

	/**
	 *
	 */
	private function registerFilesystemAlias()
	{
		$this->app->instance(Filesystem::class, $this->app['files']);
	}

	/**
	 *
	 */
	private function registerPipelineAlias()
	{
		$this->app->bind(PipelineContract::class, function() {
			return $this->app->make(PipelineConcrete::class, [$this->app]);
		});
	}

	/**
	 *
	 */
	private function registerContainerAlias()
	{
		$this->app->singleton(Container::class, function() {
			return $this->app;
		});
	}
}

