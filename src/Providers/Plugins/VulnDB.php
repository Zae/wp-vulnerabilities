<?php namespace Zae\WPVulnerabilities\Providers\Plugins;

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
		return "plugins/{$entity->getName()}/";
	}

	protected function isVulnerable($response_object, Entity $entity, array &$vulnerabilities = null)
	{
		return $this->vulnDB->isVulnerable($response_object->{$entity->getName()}->vulnerabilities, $entity->getVersion(), $vulnerabilities);
	}
}