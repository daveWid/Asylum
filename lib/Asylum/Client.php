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
	 * @var string  The public API key
	 */
	public $public_key;

	/**
	 * @var string  The private API key
	 */
	private $private_key;

	/**
	 * @var array  The array of data to send with the request
	 */
	private $data;

	/**
	 * Constructor, with public and private keys.
	 *
	 * @param string $endpoint    The endpoint to send to
	 * @param string $public_key  The public key used for authentication
	 * @param string $private_key The private key used for hashing
	 */
	public function __construct($public_key, $private_key)
	{
		$this->public_key = $public_key;
		$this->private_key = $private_key;

		$this->data = array();
	}

	/**
	 * Prepares data for a request.
	 *
	 * @param  string $uri    The uri to hit
	 * @param  string $method The HTTP method (verb)
	 * @param  array  $data   The data to send
	 * @return array          The prepared data
	 */
	public function prepare($uri, $method, array $data)
	{
		$data += array(
			'key' => $this->public_key,
			'timestamp' => time()
		);

		$data['hash'] = Hash::generate($uri, $method, $data, $this->private_key);

		return $data;
	}

}
