<?php

namespace Asylum;

/**
 * Hashing functionality for generating checksums on the data.
 *
 * @package   Asylum
 * @author    Dave Widmer <dave@davewidmer.net>
 */
class Hash
{
	/**
	 * Genearates a secure hash of the data you plan to send to the REST call.
	 *
	 * @param  string $uri          The uri you are sending the request to without the domain (i.e /beers/:id)
	 * @param  string $method       The HTTP verb you are using (i.e GET)
	 * @param  array  $data         The data to hash
	 * @param  string $private_key  The private key
	 * @return string               A SHA256 hash of your data
	 */
	public static function generate($uri, $method, $data, $private_key)
	{
		$data['uri'] = $uri;
		$data['method'] = $method;

		// Sort the data by key
		ksort($data);

		$hash = hash_hmac('sha256', http_build_query($data), $private_key, true);
		return base64_encode($hash);
	}

	/**
	 * Does a check on the generated hashs to ensure integrity.
	 *
	 * @param  string $first  The first hash
	 * @param  string $second The second hash
	 * @return boolean        Do they hashes match
	 */
	public static function is_match($first, $second)
	{
		return base64_decode($first) === base64_decode($second);
	}

}