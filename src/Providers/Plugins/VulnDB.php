<?php namespace Zae\WPVulnerabilities\Providers\Plugins;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Closure;
use Composer\Semver\Comparator;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

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
	 * VulnDB constructor.
	 *
	 * @param Client $http
	 * @param Comparator $semver
	 */
	public function __construct(Client $http, Comparator $semver)
	{
		$this->http = $http;
		$this->semver = $semver;
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
		foreach ($plugins as &$plugin) {
			$r = $this->http->get("https://wpvulndb.com/api/v2/plugins/{$plugin->getName()}/");

			$r_obj = json_decode((string)$r->getBody());

			if ($this->isVulnerable($r_obj->{$plugin->getName()}->vulnerabilities, $plugin->getVersion(), $vulnerabilities) ) {
				$plugin->vulnerable(join(', ', array_map(function($p) {
					return $p->title;
				}, $vulnerabilities->toArray())));
			}
		}

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