<?php namespace Zae\WPVulnerabilities\Services;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Composer\Semver\Comparator;
use Illuminate\Support\Collection;

class VulnDBService
{
	/**
	 * @var Comparator
	 */
	private $semver;

	/**
	 * VulnDBService constructor.
	 *
	 * @param Comparator $semver
	 */
	public function __construct(Comparator $semver)
	{
		$this->semver = $semver;
	}

	/**
	 * @param      $vulnerabilities
	 * @param      $version
	 * @param null $found_vulnerabilities
	 *
	 * @return bool
	 */
	public function isVulnerable($vulnerabilities, $version, &$found_vulnerabilities = null)
	{
		$found_vulnerabilities = Collection::make($vulnerabilities)->filter(function ($v) use ($version) {
			return $this->semver->lessThan($version, $v->fixed_in);
		});

		return $found_vulnerabilities->count() > 0;
	}
}