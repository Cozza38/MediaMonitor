<?php

namespace MediaMonitor\Factory;

use MediaMonitor\Twig\Config;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TwigFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $config = $sl->get('config');

        if (isset($config['twig'])) {
            $config = new Config($config['twig']);
        } else {
            $config = new Config();
        }

        $loader = new Twig_Loader_Filesystem($config->getTemplatePaths());
        $twig = new Twig_Environment($loader, $config->getOptions());
        return $twig;
    }
}
