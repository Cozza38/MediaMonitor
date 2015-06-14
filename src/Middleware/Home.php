<?php

namespace MediaMonitor\Middleware;

use Middleware\Middleware;

class Home
{
    public function __invoke($req, $res, $next)
    {
        if ($req->getUri()->getPath() !== '/') {
            return $next($req, $res);
        }
        ob_start();
        include 'view/index.phtml';
        $content = ob_get_clean();
        return $res->end($content);
    }
}

