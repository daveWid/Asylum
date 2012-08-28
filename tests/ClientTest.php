<?php

class ClientTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Asylum\Client  The Asylum client
	 */
	public $client;

	public function setUp()
	{
		parent::setUp();

		// User real keys in production
		$this->client = new \Asylum\Client("publickey", "privatekey");
	}

	public function testPrepare()
	{
		$query = array(
			'type' => "beer",
			'q' => "Sam Adams"
		);

		$data = $this->client->prepare("search", "GET", $query);

		// Make user they add keys in the prepare statement
		$keys = array('key', 'timestamp', 'hash');

		foreach ($keys as $key)
		{
			$this->assertArrayHasKey($key, $data);
		}
	}
}