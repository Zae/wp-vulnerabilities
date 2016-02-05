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
use Symfony\Component\Console\Helper\TableStyle;
use Zae\WPVulnerabilities\Config;
use Zae\WPVulnerabilities\Entities\Entity;
use Zae\WPVulnerabilities\Providers\General\FilterNotVulnerable;

/**
 * Class ScanCommand
 *
 * @package Zae\WPVulnerabilities\Commands
 */
class ScanThemesCommand extends Command
{
	/**
	 * @var string
	 */
	protected $signature = 'scan:themes';

	/**
	 * @var string
	 */
	protected $name = 'scan:themes';

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
		$themes = $this->pipeline->send([])
			->through($this->config->get('themes.providers'))
			->then(function($resolved_themes) {
				return $resolved_themes;
			});

		$vulnerable_themes = Collection::make($themes)
										->map(function(Entity $theme){
											return [
												$theme->getName(),
												$theme->getTitle(),
												$theme->getMessage()
											];
										});

		if ($vulnerable_themes->count() > 0) {
			$this->error('Vulnerable Themes Found!');
			$this->table([
				'name', 'title', 'message'
			], $vulnerable_themes);

			return 1;
		}

		return 0;
	}

}