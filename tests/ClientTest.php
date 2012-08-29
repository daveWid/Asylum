<?php

class ClientTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Asylum\Client  The Asylum client
	 */
	public $client;

	/**
	 * @var array  The data to test with
	 */
	public $data;

	public function setUp()
	{
		parent::setUp();

		// Just testing with the brewery db api (not affiliated....)
		$this->client = new \Asylum\Client("http://api.brewerydb.com/v2/", "publickey", "privatekey");
		$this->data = array('type' => "beer", 'q' => "Sam Adams");
	}

	public function testPrepare()
	{
		$headers = $this->client->prepare('search', 'GET', $this->data)->get_headers();

		// Make sure the header have the required oauth_* headers
		$keys = array('oauth_consumer_key', 'oauth_signature_method','oauth_timestamp',
			'oauth_nonce', 'oauth_version', 'oauth_signature');

		foreach ($keys as $key)
		{
			$this->assertArrayHasKey($key, $headers);
		}
	}

	public function testAuthorizationHeader()
	{
		// This is hard to test, just make sure it isn't empty
		$headers = $this->client->prepare('search', 'GET', $this->data)->get_headers();

		$this->assertNotEmpty($this->client->get_authorization_header());
	}
}