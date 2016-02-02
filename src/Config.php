<?php namespace Zae\WPVulnerabilities;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Support\Arr;

/**
 * Class Config
 *
 * @package Zae\WPVulnerabilities
 */
class Config
{
	/**
	 * @var array
	 */
	private $config = [];

	/**
	 * Config constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
	{
		$this->config = $config;
	}

	/**
	 * @param      $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		return Arr::get($this->config, $key, $default);
	}
}