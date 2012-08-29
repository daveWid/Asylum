<?php

class ServerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var \Asylum\Server
	 */
	public $server;

	/**
	 * @var \Asylum\Client
	 */
	public $client;

	public $data;
	public $auth;

	public function setUp()
	{
		parent::setUp();

		// I <3 the brewery db api (not affiliated....)
		$this->client = new \Asylum\Client("http://api.brewerydb.com/v2/", "publickey", "privatekey");

		// These next 2 lines would really come from the server of course...
		$data = array('type' => "beer",'q' => "Sam Adams");
		$this->data = $this->client->prepare("search", "GET", $data);

		/**
		 * // This is how you would really do it
		 * // (or hopefully your framework normalizes this data)
		 * $this->data = $_GET | $_POST depending on the request method...
		 */

		$this->server = new \Asylum\Server($this->data, 'privatekey');
	}

	public function testHasRequiredKeys()
	{
		// Make sure all of the keys are present
		$keys = array('oauth_consumer_key', 'oauth_signature_method','oauth_timestamp',
			'oauth_nonce', 'oauth_version', 'oauth_signature', 'type', 'q');

		$this->assertTrue($this->server->has_keys($keys));
	}

	public function testMissingRequiredKeys()
	{
		$this->assertFalse($this->server->has_keys(array('goingtofail')));
	}

	public function testActive()
	{
		$this->assertTrue($this->server->is_active());
	}

	public function testExpired()
	{
		// Make the request only good for 1 second...
		$server = new \Asylum\Server($this->data, 'privatekey', 1);
		sleep(2);

		$this->assertFalse($server->is_active());
	}

	public function testValidRequest()
	{
		$this->assertTrue($this->server->is_valid('http://api.brewerydb.com/v2/search', 'GET'));
	}

	public function testInvalidRequestOnDifferentURI()
	{
		$this->assertFalse($this->server->is_valid('http://api.brewerydb.com/v2/beer', 'GET'));
	}

	public function testInvalidRequestOnDifferentMethod()
	{
		$this->assertFalse($this->server->is_valid('http://api.brewerydb.com/v2/search', 'PUT'));
	}

	public function testInvalidRequestOnTamperedData()
	{
		$data = $this->data;

		// The data is tampered with
		$data['q'] = "Bells";

		$this->server = new \Asylum\Server($data, 'privatekey');
		$this->assertFalse($this->server->is_valid('http://api.brewerydb.com/v2/search', 'GET'));
	}

}
