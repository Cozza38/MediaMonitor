<?php

namespace MediaMonitor\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stratigility\MiddlewarePipe;

class MiddlewarePipeFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $pipe = new MiddlewarePipe();

        $pipe->pipe('/', $sl->get('Middleware.Home'));

        $pipe->pipe('/', function($error, $req, $res, $next) {
            return $res->end('Not Found');
        });

        return $pipe;
    }
}

