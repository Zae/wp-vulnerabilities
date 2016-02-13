<?php namespace Zae\WPVulnerabilities\Providers\General;

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
use Zae\WPVulnerabilities\Services\VulnDBApi;
use Zae\WPVulnerabilities\Services\VulnDBService;

/**
 * Class VulnDB
 *
 * @package Zae\WPVulnerabilities\Providers\Plugins
 */
abstract class VulnDB
{
	/**
	 * @var Client
	 */
	private $vulnDBApi;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var VulnDBService
	 */
	protected $vulnDB;

	/**
	 * VulnDB constructor.
	 *
	 * @param VulnDBApi     $vulnDBApi
	 * @param Config        $config
	 * @param VulnDBService $vulnDB
	 */
	public function __construct(VulnDBApi $vulnDBApi, Config $config, VulnDBService $vulnDB)
	{
		$this->vulnDBApi = $vulnDBApi;
		$this->config = $config;
		$this->vulnDB = $vulnDB;
	}

	/**
	 * @param         $entities
	 * @param Closure $next
	 *
	 * @return mixed
	 */
	public function handle(array $entities, Closure $next)
	{
		$entities = $next($entities);

		$entities = $this->findVulnerabilities($entities);

		return $entities;
	}

	/**
	 * @param $entities
	 *
	 * @return mixed
	 */
	private function findVulnerabilities(array $entities)
	{
		$requests = Collection::make($entities)->map(function (Entity $entity) {
			return function () use ($entity) {
				return $this->vulnDBApi->getAsync($this->getAPIPath($entity));
			};
		})->getIterator();

		(new Pool($this->vulnDBApi, $requests, [
			'concurrency' => $this->config->get('http.concurrency'),
			'fulfilled' => function ($response, $index) use (&$entities) {
				if ($response->getStatusCode() === 200) {
					$response_object = json_decode((string)$response->getBody());

					if ($this->isVulnerable($response_object, $entities[$index], $vulnerabilities)) {
						$entities[$index]->vulnerable(Collection::make($vulnerabilities)->implode('title', ','));
					}
				}
			}
		]))->promise()->wait();

		return $entities;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return string
	 */
	abstract protected function getAPIPath(Entity $entity);

	/**
	 * @param        $response_object
	 * @param Entity $entity
	 * @param array  $vulnerabilities
	 *
	 * @return mixed
	 */
	abstract protected function isVulnerable($response_object, Entity $entity, array &$vulnerabilities = null);
}