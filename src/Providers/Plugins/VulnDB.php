<?php namespace Zae\WPVulnerabilities\Providers\Plugins;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use Composer\Semver\Comparator;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Illuminate\Support\Collection;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Plugin;

/**
 * Class VulnDB
 *
 * @package Zae\WPVulnerabilities\Providers\Plugins
 */
class VulnDB
{
	/**
	 * @var Client
	 */
	private $http;
	/**
	 * @var Comparator
	 */
	private $semver;
	/**
	 * @var Config
	 */
	private $config;

	/**
	 * VulnDB constructor.
	 *
	 * @param Client $http
	 * @param Comparator $semver
	 */
	public function __construct(Client $http, Comparator $semver, Config $config)
	{
		$this->http = $http;
		$this->semver = $semver;
		$this->config = $config;
	}

	/**
	 * @param         $plugins
	 * @param Closure $next
	 *
	 * @return mixed
	 */
	public function handle($plugins, Closure $next)
	{
		$plugins = $next($plugins);

		$plugins = $this->findVulnerabilities($plugins);

		return $plugins;
	}

	/**
	 * @param $plugins
	 *
	 * @return mixed
	 */
	private function findVulnerabilities($plugins)
	{
		$requests = Collection::make($plugins)->map(function(Plugin $plugin)
		{
			return function() use ($plugin)
			{
				return $this->http->getAsync("https://wpvulndb.com/api/v2/plugins/{$plugin->getName()}/", [
					'exceptions' => false
				]);
			};
		})->getIterator();

		(new Pool($this->http, $requests, [
			'concurrency' => $this->config->get('http.concurrency'),
			'fulfilled' => function ($response, $index) use (&$plugins) {
				if ($response->getStatusCode() === 200) {
					$response_object = json_decode((string)$response->getBody());

					if ($this->isVulnerable($response_object->{$plugins[$index]->getName()}->vulnerabilities, $plugins[$index]->getVersion(), $vulnerabilities)) {
						$plugins[$index]->vulnerable(Collection::make($vulnerabilities)->implode('title', ','));
					}
				}
			}
		]))->promise()->wait();

		return $plugins;
	}

	/**
	 * @param      $vulnerabilities
	 * @param      $version
	 * @param null $found_vulnerabilities
	 *
	 * @return bool
	 */
	private function isVulnerable($vulnerabilities, $version, &$found_vulnerabilities = null)
	{
		$found_vulnerabilities = Collection::make($vulnerabilities)->filter(function($v) use ($version) {
			return $this->semver->lessThan($version, $v->fixed_in);
		});

		return $found_vulnerabilities->count() > 0;
	}
}