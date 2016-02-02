<?php namespace Zae\WPVulnerabilities\Commands;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Plugin;
use Zae\WPVulnerabilities\Providers\Plugins\FilterNotVulnerable;

/**
 * Class ScanCommand
 *
 * @package Zae\WPVulnerabilities\Commands
 */
class ScanPluginsCommand extends Command
{
	/**
	 * @var string
	 */
	protected $signature = 'scan:plugins';

	/**
	 * @var string
	 */
	protected $name = 'scan:plugins';

	/**
	 * @var string
	 */
	protected $description = 'Scan for known vulnerabilities';

	/**
	 * @var Client
	 */
	private $http;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * @var Pipeline
	 */
	private $pipeline;

	/**
	 * ScanCommand constructor.
	 *
	 * @param Client $http
	 * @param Config $config
	 * @param \Illuminate\Filesystem\Filesystem $filesystem
	 * @param Pipeline $pipeline
	 */
	public function __construct(Client $http, Config $config, \Illuminate\Filesystem\Filesystem $filesystem, Pipeline $pipeline)
	{
		parent::__construct();

		$this->http = $http;
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->pipeline = $pipeline;
	}

	/**
	 * @return int
	 */
	public function handle()
	{
		$plugins = $this->pipeline->send([])
			->through(array_merge([FilterNotVulnerable::class], $this->config->get('plugins.providers')))
			->then(function($resolved_plugins) {
				return $resolved_plugins;
			});

		$vulnerable_plugins = Collection::make($plugins)
										->map(function(Plugin $plugin){
											return [
												$plugin->getName(),
												$plugin->getTitle(),
												$plugin->getMessage()
											];
										});

		if ($vulnerable_plugins->count() > 0) {
			$this->table([
				'name', 'title', 'message'
			], $vulnerable_plugins);

			return 1;
		}

		return 0;
	}

}