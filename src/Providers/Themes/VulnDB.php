<?php namespace Zae\WPVulnerabilities\Providers\Themes;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Illuminate\Support\Collection;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Entities\Entity;
use Zae\WPVulnerabilities\Services\VulnDBService;

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
	 * @var Config
	 */
	private $config;

	/**
	 * @var VulnDBService
	 */
	private $vulnDB;

	/**
	 * VulnDB constructor.
	 *
	 * @param Client        $http
	 * @param Config        $config
	 * @param VulnDBService $vulnDB
	 */
	public function __construct(Client $http, Config $config, VulnDBService $vulnDB)
	{
		$this->http = $http;
		$this->config = $config;
		$this->vulnDB = $vulnDB;
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
		$requests = Collection::make($plugins)->map(function (Entity $plugin) {
			return function () use ($plugin) {
				return $this->http->getAsync("https://wpvulndb.com/api/v2/themes/{$plugin->getName()}/", [
					'exceptions' => false
				]);
			};
		})->getIterator();

		(new Pool($this->http, $requests, [
			'concurrency' => $this->config->get('http.concurrency'),
			'fulfilled' => function ($response, $index) use (&$plugins) {
				if ($response->getStatusCode() === 200) {
					$response_object = json_decode((string)$response->getBody());

					if ($this->vulnDB->isVulnerable($response_object->{$plugins[$index]->getName()}->vulnerabilities, $plugins[$index]->getVersion(), $vulnerabilities)) {
						$plugins[$index]->vulnerable(Collection::make($vulnerabilities)->implode('title', ','));
					}
				}
			}
		]))->promise()->wait();

		return $plugins;
	}
}

