<?php

namespace MediaMonitor\Factory;

use Zend\Diactoros\Server;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $server = Server::createServer(
            $sl->get('middlewarepipe'),
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        return $server;
    }
}
