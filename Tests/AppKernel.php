<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(new Psamatt\YamlExportBundle\PsamattYamlExportBundle(),);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // we dont want to load any config files
    }
}
