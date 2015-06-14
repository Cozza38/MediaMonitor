<?php

namespace MediaMonitor\Middleware\Factory;

use MediaMonitor\Middleware\Home;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HomeFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $home = new Home($sl->get('twig'));
        return $home;
    }
}
