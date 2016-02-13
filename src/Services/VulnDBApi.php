<?php namespace Zae\WPVulnerabilities\Services;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra
 */

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class VulnDBApi implements ClientInterface
{
	/**
	 * @var Client
	 */
	private $http;

	/**
	 * VulnDNApi constructor.
	 *
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		$this->http = $client;
	}

	function __call($name, $arguments)
	{
		return call_user_func_array([$this->http, $name], $arguments);
//		return $this->http->{$name}(...$arguments);
	}

	/**
	 * Send an HTTP request.
	 *
	 * @param RequestInterface $request Request to send
	 * @param array            $options Request options to apply to the given
	 *                                  request and to the transfer.
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function send(RequestInterface $request, array $options = [])
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	/**
	 * Asynchronously send an HTTP request.
	 *
	 * @param RequestInterface $request Request to send
	 * @param array            $options Request options to apply to the given
	 *                                  request and to the transfer.
	 *
	 * @return PromiseInterface
	 */
	public function sendAsync(RequestInterface $request, array $options = [])
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	/**
	 * Create and send an HTTP request.
	 *
	 * Use an absolute path to override the base path of the client, or a
	 * relative path to append to the base path of the client. The URL can
	 * contain the query string as well.
	 *
	 * @param string              $method  HTTP method
	 * @param string|UriInterface $uri     URI object or string.
	 * @param array               $options Request options to apply.
	 *
	 * @return ResponseInterface
	 * @throws GuzzleException
	 */
	public function request($method, $uri, array $options = [])
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	/**
	 * Create and send an asynchronous HTTP request.
	 *
	 * Use an absolute path to override the base path of the client, or a
	 * relative path to append to the base path of the client. The URL can
	 * contain the query string as well. Use an array to provide a URL
	 * template and additional variables to use in the URL template expansion.
	 *
	 * @param string              $method  HTTP method
	 * @param string|UriInterface $uri     URI object or string.
	 * @param array               $options Request options to apply.
	 *
	 * @return PromiseInterface
	 */
	public function requestAsync($method, $uri, array $options = [])
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}

	/**
	 * Get a client configuration option.
	 *
	 * These options include default request options of the client, a "handler"
	 * (if utilized by the concrete client), and a "base_uri" if utilized by
	 * the concrete client.
	 *
	 * @param string|null $option The config option to retrieve.
	 *
	 * @return mixed
	 */
	public function getConfig($option = null)
	{
		return $this->__call(__FUNCTION__, func_get_args());
	}
}