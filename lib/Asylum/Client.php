<?php

namespace Asylum;

/**
 * Prepares the client data to be sent to the server.
 *
 * @package   Asylum
 * @author    Dave Widmer <dave@davewidmer.net>
 */
class Client
{
	/**
	 * @var string  The endpoint url
	 */
	public $endpoint;

	/**
	 * @var string  The public API key
	 */
	public $public_key;

	/**
	 * @var string  The private API key
	 */
	private $private_key;

	/**
	 * @var array  The oauth headers
	 */
	private $headers;

	/**
	 * @var string  The authorization header string
	 */
	private $authorization;

	/**
	 * Constructor, with public and private keys.
	 *
	 * @param string $endpoint    The endpoint to send to
	 * @param string $public_key  The public key used for authentication
	 * @param string $private_key The private key used for hashing
	 */
	public function __construct($endpoint, $public_key, $private_key)
	{
		$this->endpoint = $endpoint;
		$this->public_key = $public_key;
		$this->private_key = $private_key;
	}

	/**
	 * @return array
	 */
	public function get_headers()
	{
		return $this->headers;
	}

	/**
	 * @return string  The authorization header to send with the request
	 */
	public function get_authorization_header()
	{
		$auth = array();
		foreach ($this->headers as $key => $value) {
			$auth[] = $key.'="'.$value.'"';
		}

		return "OAuth ".join(',', $auth);
	}

	/**
	 * Prepares data for a request.
	 *
	 * @param  string $uri    The uri to hit
	 * @param  string $method The HTTP method (verb)
	 * @param  array  $data   The data to send
	 * @return Asylum\Client  $this....
	 */
	public function prepare($uri, $method, array $data)
	{
		/**
		 * Required oauth headers
		 * @link   http://tools.ietf.org/html/rfc5849#section-3.1
		 */
		$headers = array(
			'oauth_consumer_key' => $this->public_key,
			'oauth_token' => '',
			'oauth_signature_method' => "HMAC-SHA256",
			'oauth_timestamp' => time(),
			'oauth_nonce' => '',
			'oauth_version' => '1.0'
		);

		$headers['oauth_signature'] = Hash::generate(
			$this->endpoint.$uri,
			$method,
			$data + $headers,
			$this->private_key
		);

		$this->headers = $headers;
		return $this;
	}

}
