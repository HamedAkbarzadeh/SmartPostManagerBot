<?php

namespace src\app\Trait;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


trait Log
{
    public function setLog($text, $path = "../Log/All.log", $level = 'INFO')
    {
        $logger = new Logger('my_logger');

        $logger->pushHandler(new StreamHandler($path, $this->levelAttribute($level)));

        $logger->info($text);
    }

    public function levelAttribute($level)
    {
        switch ($level) {
            case 'INFO':
                $result = Logger::INFO;
                break;
            case 'EMERGENCY':
                $result = Logger::EMERGENCY;
                break;
            case 'ALERT':
                $result = Logger::ALERT;
                break;
            case 'CRITICAL':
                $result = Logger::CRITICAL;
                break;
            case 'ERROR':
                $result = Logger::ERROR;
                break;
            case 'WARNING':
                $result = Logger::WARNING;
                break;
            case 'NOTICE':
                $result = Logger::NOTICE;
                break;
            case 'DEBUG':
                $result = Logger::DEBUG;
                break;
            default:
                $result = Logger::INFO;
                break;
        }
        return $result;
    }
}
