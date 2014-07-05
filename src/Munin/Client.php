<?php

namespace Munin;

use Stream\Stream;

/**
 * @author KonstantinKuklin <konstantin.kuklin@gmail.com>
 */
class Client implements ClientInterface
{
    private $stream = null;
    private $driver = null;
    private $pluginsList = null;
    private $pluginsListFliped = null;
    private $readTimes = 5;

    const COMMAND_FETCH = 'fetch';
    const COMMAND_LIST = 'list';
    const COMMAND_CONFIG = 'config';
    const COMMAND_VERSION = 'version';

    const END_LINE = "\n";
    const SPACE = ' ';
    const PLUGIN_DELIMITER = '.value ';

    /**
     * @param string $host
     * @param int    $port
     *
     * @throws \Stream\Exceptions\ConnectionStreamException
     * @throws \Stream\Exceptions\StreamException
     */
    public function __construct($host, $port = 4949)
    {
        $this->driver = new Driver();
        $this->stream = new Stream($host, Stream::PROTOCOL_TCP, $port, $this->driver);
        $this->stream->open();
        $this->stream->setBlockingOff();
        $this->stream->setReadTimeOut(3);
    }

    /**
     * @param int $seconds
     * @param int $microseconds
     *
     * @throws \Stream\Exceptions\StreamException
     */
    public function setTimeOut($seconds, $microseconds = 0)
    {
        $this->getStream()->setReadTimeOut($seconds, $microseconds);
    }

    /**
     * @param $times
     *
     * @return bool
     */
    public function setReadTimes($times)
    {
        if (!is_int($times) && $times < 1) {
            return false;
        }
        $this->readTimes = $times;

        return true;
    }

    /**
     * @return array
     */
    public function getPluginsList()
    {
        // get from cache
        if (!empty($this->pluginsList)) {
            return $this->pluginsList;
        }

        $pluginsResponse = $this->getResponse(self::COMMAND_LIST);
        // plugins list always were sent second row
        if (!isset($pluginsResponse[1])) {
            return array();
        }
        $pluginsList = explode(self::SPACE, $pluginsResponse[1]);
        $this->pluginsListFliped = array_flip($pluginsList);

        return $pluginsList;
    }

    /**
     * @param $plugin
     *
     * @return array
     */
    public function getPluginValue($plugin)
    {
        // get from cache is in cache array
        if (isset($this->pluginsListFliped[$plugin]) && is_array($this->pluginsListFliped[$plugin])) {
            return $this->pluginsListFliped[$plugin];
        }

        $pluginResponse = $this->getResponse(self::COMMAND_FETCH, $plugin);
        $values = array();
        foreach ($pluginResponse as $pluginRow) {
            if (false !== strpos($pluginRow, self::PLUGIN_DELIMITER)) {
                $pluginInfo = explode(self::PLUGIN_DELIMITER, $pluginRow);
                $values[$pluginInfo[0]] = $pluginInfo[1];
            }
        }

        // add to cache if not empty
        if (!empty($values)) {
            $this->pluginsListFliped[$plugin] = $values;
        }

        return $values;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $versionResponseList = $this->getResponse(self::COMMAND_VERSION);
        // version info always in second row
        if (!isset($versionResponseList[1])) {
            return false;
        }
        $list = explode(self::SPACE, $versionResponseList[1]);

        // return last value of last response string
        return array_pop($list);
    }

    /**
     * @param $plugin
     *
     * @return array
     */
    public function getPluginConfig($plugin)
    {
        // TODO I don't have any idea what to do with it and what will be need from it for anybody, whats why just return dirty strings
        return $this->getResponse(self::COMMAND_CONFIG, $plugin);
    }

    /**
     * @return int
     */
    public function getPluginsCount()
    {
        if ($this->pluginsList === null) {
            $this->getPluginsList();
        }

        return count($this->pluginsList);
    }

    /**
     * @param string $command
     * @param mixed  $param
     *
     * @return array
     * @throws \Stream\Exceptions\NotStringStreamException
     * @throws \Stream\Exceptions\StreamException
     */
    private function getResponse($command, $param = null)
    {
        $this->getStream()->sendContents(sprintf('%s%s', $command, (empty($param) ? '' : ' ' . $param)));

        $contents = '';
        $isReady = true;
        // try to read socket while result is not false
        for ($i = 0; $i < $this->readTimes; $i++) {
            // try read if last try was good
            if ($isReady) {
                if ($isReady = $this->getStream()->isReadyForReading()) {
                    // check false = error
                    if (false !== ($content = $this->getStream()->getContentsByStreamGetContents(1024))) {
                        $contents .= $content;
                    }
                }
            }
        }

        return explode(self::END_LINE, $contents);
    }

    /**
     * @return Stream
     */
    private function getStream()
    {
        return $this->stream;
    }
} 