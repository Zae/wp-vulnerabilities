<?php namespace Zae\WPVulnerabilities\Entities;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

/**
 * Class Wordpress
 *
 * @package Zae\WPVulnerabilities
 */
class Wordpress extends Entity
{
	/**
	 * @return string
	 */
	public function getSerializedVersion()
	{
		return str_replace('.', '', $this->getVersion());
	}

}