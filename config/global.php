<?php

use MediaMonitor\Middleware\Factory as MiddlewareFactory;
use MediaMonitor\Factory;

return array(
    'service_manager' => array(
        "invokables" => array(
        ),
        "aliases" => array(
        ),
        "factories" => array(
            "server" => Factory\ServerFactory::class,
            "middlewarepipe" => Factory\MiddlewarePipeFactory::class,
            "twig" => Factory\TwigFactory::class,
            "Middleware.Home" => MiddlewareFactory\HomeFactory::class,
        ),
    )
);
