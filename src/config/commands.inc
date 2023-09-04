<?php

use SemiorbitFwkConsole\CacheClear;
use SemiorbitFwkConsole\CopyLang;
use SemiorbitFwkConsole\MakeAll;
use SemiorbitFwkConsole\MakeConfig;
use SemiorbitFwkConsole\MakeController;
use SemiorbitFwkConsole\MakeLang;
use SemiorbitFwkConsole\MakeModel;
use SemiorbitFwkConsole\MakePackage;
use SemiorbitFwkConsole\UseGlobals;

return [

    'make:model' => MakeModel::class,

    'mdl' => MakeModel::class,

    'cache:clear' => CacheClear::class,

    'make:lang' => MakeLang::class,

    'lng' => MakeLang::class,

    //Clear framework cache

    'ccf' => CacheClear::class,

    // Clear config cache

    'ccc' => CacheClear::class,

    'cc' => CacheClear::class,

    'make:config' => MakeConfig::class,

    'cfg' => MakeConfig::class,

    'make:package' => MakePackage::class,

    'pkg' => MakePackage::class,

    'use' => UseGlobals::class,

    'make:controller' => MakeController::class,

    'ctrl' => MakeController::class,

    'mk' => MakeAll::class,

    'cplng' => CopyLang::class

];