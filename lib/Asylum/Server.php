<?php

namespace Asylum;

/**
 * Consume the client data on the server side.
 *
 * @package   Asylum
 * @author    Dave Widmer <dave@davewidmer.net>
 */
class Server
{
	/**
	 * @var string  The private key
	 */
	private $private_key;

	/**
	 * @var array   The request data
	 */
	private $data;

	/**
	 * @var int    How long is the request active for (in seconds)
	 */
	private $active_for;

	/**
	 * Constructor with private key.
	 *
	 * @param array  $data         The request data
	 * @param string $private_key  The private key
	 * @param int    $active_for   The time in seconds that the request is active (in seconds)
	 */
	public function __construct(array $data, $private_key, $active_for = 600)
	{
		$this->data = $data;
		$this->private_key = $private_key;
		$this->active_for = $active_for;
	}

	/**
	 * Does the data have the specified keys?
	 *
	 * @param  array   $keys   The keys to look for in the data
	 * @return boolean
	 */
	public function has_keys(array $keys)
	{
		$success = true;

		foreach ($keys as $key)
		{
			if ( ! array_key_exists($key, $this->data))
			{
				$success = false;
				break;
			}
		}

		return $success;
	}

	/**
	 * Make sure the request is active.
	 *
	 * @return boolean
	 */
	public function is_active()
	{
		return (time() + $this->active_for) > $this->data['timestamp'];
	}

	/**
	 * Checks to see if the request from the server is valid.
	 *
	 * @param  string  $uri     The uri that was requested
	 * @param  string  $method  The request method
	 * @return boolean          The request validity
	 */
	public function is_valid($uri, $method)
	{
		$data = $this->data;

		$hash = $data['hash'];
		unset($data['hash']);

		$checksum = Hash::generate($uri, $method, $data, $this->private_key);
		return Hash::is_match($hash, $checksum);
	}

}
