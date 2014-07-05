<?php
/**
 * @author KonstantinKuklin <konstantin.kuklin@gmail.com>
 */

namespace Munin\Tests;

use Munin\Client;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    private $host = 'munin.test';

    public function testGetPluginDfValue()
    {
        $client = new Client($this->host, 4949);
        $client->setTimeOut(6);

        $pluginValue = $client->getPluginValue('df');
        $this->assertTrue((count($pluginValue) > 0), 'Fail, got wrong plugin df value.');
    }

}