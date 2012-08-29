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
	 * @link   http://tools.ietf.org/html/rfc5849#section-3.4.1.1
	 *
	 * @param  string $uri          The uri you are sending the request to without the domain (i.e /beers/:id)
	 * @param  string $method       The HTTP verb you are using (i.e GET)
	 * @param  array  $data         The data to hash
	 * @param  string $private_key  The private key
	 * @return string               A hash of your data
	 */
	public static function generate($uri, $method, $data, $private_key)
	{
		// Sort the data by byte-order
		ksort($data);

		// Building the string the OAuth way, see @link tag above for more
		$authorization = array(
			strtoupper($method),
			urlencode($uri),
			urlencode(http_build_query($data))
		);

		$method = strtolower(str_replace("HMAC-", "", $data['oauth_signature_method']));

		$hash = hash_hmac($method, join('&', $authorization), $private_key, true);
		return urlencode(base64_encode($hash));
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