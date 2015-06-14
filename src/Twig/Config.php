<?php

namespace MediaMonitor\Twig;

class Config
{
    protected $cacheDir      = 'data/cache/twig';
    protected $debugMode     = false;
    protected $templatePaths = array();

    /**
     * __construct
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (array_key_exists('template_paths', $config)) {
            $this->setTemplatePaths($config['template_paths']);
        }

        if (array_key_exists('debug', $config)) {
            $this->setDebugMode($config['debug']);
        }

    }

    /**
     * getTemplatePaths
     *
     * @return array
     */
    public function getTemplatePaths()
    {
        return $this->templatePaths;
    }

    /**
     * setTemplatePaths
     *
     * @param array $paths
     */
    public function setTemplatePaths(array $paths)
    {
        $this->templatePaths = $paths;
    }

    /**
     * getOptions
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            'debug' => $this->debugMode,
            'cache' => $this->cacheDir,
        );
    }

    public function getDebugMode()
    {
        return $this->debugMode;
    }

    public function setDebugMode($flag)
    {
        $this->debugMode = (bool) $flag;
    }
}

