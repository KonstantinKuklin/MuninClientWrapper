<?php
/**
 * @author KonstantinKuklin <konstantin.kuklin@gmail.com>
 */

namespace Munin\Tests;

use Munin\Client;

class CommonTest extends \PHPUnit_Framework_TestCase
{
    private $host = 'munin.test';
    private $client = null;

    public function __construct()
    {
        $this->client = new Client($this->host, 4949);
        $this->client->setTimeOut(6);
    }

    public function testGetVersion()
    {
        $version = $this->getClient()->getVersion();
        $this->assertEquals(1, version_compare($version, "0.1"), 'Fail, returned version is empty');
    }

    public function testGetPluginsList()
    {
        $pluginsList = $this->getClient()->getPluginsList();
        $this->assertTrue(
            (false !== array_search('cpu', $pluginsList)),
            sprintf('Fail, got wrong plugins list. %s', print_r($pluginsList, true))
        );
    }

    public function testGetPluginConfig()
    {
        $pluginConfig = $this->getClient()->getPluginConfig('cpu');
        $this->assertTrue(count($pluginConfig) > 0, 'Fail, got wrong plugin config.');
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        return $this->client;
    }
} 