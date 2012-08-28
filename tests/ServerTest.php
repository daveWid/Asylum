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

	public function setUp()
	{
		parent::setUp();

		$this->client = new \Asylum\Client('publickey', 'privatekey');
		$data = $this->client->prepare("search", "GET", array(
			'type' => "beer",
			'q' => "Sam Adams"
		));

		$this->server = new \Asylum\Server($data, 'privatekey');
	}

	public function testHasRequiredKeys()
	{
		$this->assertTrue($this->server->has_keys(array('key', 'timestamp', 'hash')));
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
		$data = $this->client->prepare('search', 'GET', array());
		$data['timestamp'] += 1000;

		$server = new \Asylum\Server($data, 'privatekey');
		$this->assertFalse($server->is_active());
	}

	public function testValidRequest()
	{
		$this->assertTrue($this->server->is_valid('search', 'GET'));
	}

	public function testInvalidRequestOnDifferentURI()
	{
		$this->assertFalse($this->server->is_valid('beer', 'GET'));
	}

	public function testInvalidRequestOnDifferentMethod()
	{
		$this->assertFalse($this->server->is_valid('search', 'PUT'));
	}

	public function testInvalidRequestOnTamperedData()
	{
		$data = $this->client->prepare("search", "GET", array(
			'type' => "beer",
			'q' => "Sam Adams"
		));

		// The data is tampered with
		$data['q'] = "Bells";

		$this->server = new \Asylum\Server($data, 'privatekey');

		$this->assertFalse($this->server->is_valid('search', 'GET'));
	}

}
