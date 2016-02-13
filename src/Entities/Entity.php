<?php namespace Zae\WPVulnerabilities\Entities;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

/**
 * Class Entity
 *
 * @package Zae\WPVulnerabilities
 */
class Entity
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var boolean
	 */
	private $vulnerable = false;

	/**
	 * @var string
	 */
	private $message = '';

	/**
	 * @var string
	 */
	private $title;

	/**
	 * Plugin constructor.
	 *
	 * @param        $name
	 * @param        $version
	 * @param string $title
	 */
	public function __construct($name, $version, $title = '')
	{
		$this->name = $name;
		$this->version = $version;
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return bool
	 */
	public function isVulnerable()
	{
		return $this->vulnerable;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param $message
	 */
	public function vulnerable($message)
	{
		$this->vulnerable = true;
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getSerializedVersion()
	{
		return str_replace('.', '', $this->getVersion());
	}

}