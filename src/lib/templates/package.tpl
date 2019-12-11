<?php
/*
 *------------------------------------------------------------------------------------------------
 * SEMIORBIT FRAMEWORK                                                     framework.semiorbit.com
 *------------------------------------------------------------------------------------------------
 */

namespace {$PKG_NS};


use Semiorbit\Component\PackageServiceProvider;

class {$PKG}ServiceProvider extends PackageServiceProvider
{

    const ServiceID = "{$PKG_ID}";


    public function Register()
    {

        $this->Package->setPath(__DIR__)

            ->setConfigPath(__DIR__ . '/config')

            ->setRoutesPath( __DIR__ . '/routes')

            ->setViewsPath(__DIR__ . '/views')

            ->setLangPath(__DIR__ . '/lang')

            ->setModelsPath(__DIR__)

            ->setControllersPath(__DIR__ . '/Http')

            ->setApiControllersDir('{$API_DIR}')

            ->setModelsNameSpace(__NAMESPACE__)

            ->setControllersNamespace(__NAMESPACE__ . '\Http');

    }

    public function onStart()
    {
        //
    }

}