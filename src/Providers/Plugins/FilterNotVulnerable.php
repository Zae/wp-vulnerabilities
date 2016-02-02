<?php namespace Zae\WPVulnerabilities\Providers\Plugins;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

/**
 * Class FilterNotVulnerable
 *
 * @package Zae\WPVulnerabilities\Providers\Plugins
 */
class FilterNotVulnerable
{
	/**
	 * @param $plugins
	 * @param $next
	 *
	 * @return array
	 */
	public function handle($plugins, $next)
	{
		return array_filter($next($plugins), function($plugin){
			return $plugin->isVulnerable();
		});
	}
}