<?php

namespace Munin;

use Stream\StreamDriverInterface;

/**
 * @author KonstantinKuklin <konstantin.kuklin@gmail.com>
 */
class Driver implements StreamDriverInterface
{
    const LINE_DELIMITER = "\n";
    const PARAM_DELIMITER = " "; // space

    /**
     * @param  string $data
     *
     * @return string
     */
    public function prepareSendData($data)
    {
        return $data . self::LINE_DELIMITER;
    }

    /**
     * @param  string $data
     *
     * @return string
     */
    public function prepareReceiveData($data)
    {
        return $data;
    }

} 