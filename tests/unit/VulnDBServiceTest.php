<?php namespace Zae\WPVulnerabilities\Tests;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Zae\WPVulnerabilities\Services\VulnDBService;

/**
 * Class VulnDBServiceTest
 *
 * @package Zae\WPVulnerabilities\Tests
 */
class VulnDBServiceTest extends TestCase
{
	/**
	 * @test
	 * @dataProvider getVulnerabilities
	 */
	public function it_correctly_detects_vulnerabilities($vulnerabilities, $version, $expected)
	{
		/** @var VulnDBService $vulnservice */
		$vulnservice = $this->container->make(VulnDBService::class);

		$this->assertEquals($expected, $vulnservice->isVulnerable($vulnerabilities, $version, $found));
	}

	/**
	 * @test
	 * @dataProvider getVulnerabilities2
	 */
	public function it_correctly_finds_vulnerabilities($vulnerabilities, $version, $expected)
	{
		/** @var VulnDBService $vulnservice */
		$vulnservice = $this->container->make(VulnDBService::class);

		$vulnservice->isVulnerable($vulnerabilities, $version, $found);

		$this->assertEquals($expected, $found->first()->fixed_in);
	}

	public function getVulnerabilities()
	{
		return [
			[[(object)['fixed_in' => '3.1.5']], '3.1.4', true, [(object)['fixed_in' => '3.1.5']]],
			[[(object)['fixed_in' => '3.1.5']], '3.1.5', false, []],
			[[(object)['fixed_in' => '3.1.5']], '3.1.6', false, []],
			[[(object)['fixed_in' => '3.2.0']], '3.1.4', true, [(object)['fixed_in' => '3.2.0']]],
			[[(object)['fixed_in' => '3.2.0']], '3.1.5', true, [(object)['fixed_in' => '3.2.0']]],
			[[(object)['fixed_in' => '3.2.0']], '3.1.6', true, [(object)['fixed_in' => '3.2.0']]],
			[[(object)['fixed_in' => '2.2.0'], (object)['fixed_in' => '4.0']], '3.1.4', true, [(object)['fixed_in' => '4.0']]],
			[[(object)['fixed_in' => '2.2.0'], (object)['fixed_in' => '4.0']], '4.2', false, []],
			[[(object)['fixed_in' => '2.2.0'], (object)['fixed_in' => '4.0']], '1.2.0', true, [(object)['fixed_in' => '2.2.0'], (object)['fixed_in' => '4.0']]],
		];
	}

	public function getVulnerabilities2()
	{
		return [
			[[(object)['fixed_in' => '3.1.5']], '3.1.4', '3.1.5'],
			[[(object)['fixed_in' => '3.2.0']], '3.1.4', '3.2.0'],
			[[(object)['fixed_in' => '3.2.0']], '3.1.5', '3.2.0'],
			[[(object)['fixed_in' => '3.2.0']], '3.1.6', '3.2.0'],
			[[(object)['fixed_in' => '2.2.0'], (object)['fixed_in' => '4.0']], '3.1.4','4.0'],
			[[(object)['fixed_in' => '2.2.0'], (object)['fixed_in' => '4.0']], '1.2.0', '2.2.0'],
		];
	}
}

