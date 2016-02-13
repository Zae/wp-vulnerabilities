<?php namespace Zae\WPVulnerabilities\Providers\Wordpress;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Zae\WPVulnerabilities\Entities\Entity;
use Zae\WPVulnerabilities\Providers\General\VulnDB as VulnDBAbstraction;

/**
 * Class VulnDB
 *
 * @package Zae\WPVulnerabilities\Providers\Plugins
 */
class VulnDB extends VulnDBAbstraction
{
	/**
	 * @param Entity $entity
	 *
	 * @return string
	 */
	protected function getAPIPath(Entity $entity)
	{
		return "wordpresses/{$entity->getSerializedVersion()}/";
	}

	protected function isVulnerable($response_object, Entity $entity, array &$vulnerabilities = null)
	{
		return $this->vulnDB->isVulnerable($response_object->{$entity->getVersion()}->vulnerabilities, $entity->getVersion(), $vulnerabilities);
	}
}
