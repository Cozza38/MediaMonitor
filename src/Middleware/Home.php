<?php

namespace MediaMonitor\Middleware;

use Middleware\Middleware;
use Twig_Environment as Twig;

class Home
{
    protected $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }


    public function __invoke($req, $res, $next)
    {
        if ($req->getUri()->getPath() !== '/') {
            return $next($req, $res);
        }

        return $res->end(
            $this->twig->render('index.html', array())
        );
    }
}

