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
		$this->data = array('type' => "beer",'q' => "Sam Adams");
		$this->auth = $this->client->prepare("search", "GET", $this->data)->get_authorization_header();

		$this->server = new \Asylum\Server($this->data, $this->auth, 'privatekey');
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
		$server = new \Asylum\Server($this->data, $this->auth, 'privatekey', 1);
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

		$this->server = new \Asylum\Server($data, $this->auth, 'privatekey');
		$this->assertFalse($this->server->is_valid('http://api.brewerydb.com/v2/search', 'GET'));
	}

}
