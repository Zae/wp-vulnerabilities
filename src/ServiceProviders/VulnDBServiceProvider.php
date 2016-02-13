<?php namespace Zae\WPVulnerabilities\ServiceProviders;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use Zae\WPVulnerabilities\Services\VulnDBApi;

class VulnDBServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(VulnDBApi::class, function()
		{
			return new VulnDBApi(new Client([
				'base_uri' => "https://wpvulndb.com/api/v2/",
				'exceptions' => false
			]));
		});
	}
}