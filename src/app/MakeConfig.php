<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Base\Application;
use Semiorbit\Cache\FrameworkCache;
use Semiorbit\Config\Config;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ModelBuilder;

class MakeConfig extends Command
{


    public function Configure()
    {
        $this->Define("{group}");
    }



    public function Execute()
    {


        $group = $this->Argument('group')->Value();

        $app_config = $group;

        $res = false;

        if ($group === 'all') {

            if ($handle = opendir($path = FW . "core/Config/Default")) {

                while (false !== ($file = readdir($handle))) {

                    if (ends_with($file, '.inc') &&

                        (Config::GROUP__FRAMEWORK !== ($group_name = substr($file, 0, strlen($file) - 4))))

                        $res = $this->CopyConfig($group_name);

                }

                closedir($handle);

            }

        } else

            $res = $this->CopyConfig($group);


        FrameworkCache::Clear('config');


        return [$res, $group, $app_config];


    }


    protected function CopyConfig($group)
    {

        if ($res = file_exists($fwk_config = FW . "core/Config/Default/{$group}.inc")) {

            if (file_exists($app_config = Application::ConfigPath() . "{$group}.inc" )) {

                $res = copy($fwk_config, $app_config = Application::ConfigPath() . "{$group}.example.inc");

            } else {

                $res = copy($fwk_config, $app_config);

            }


        }

        return $res;

    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($result, $key, $file) = $res;

        /** @var ModelBuilder $model */

        switch ($result) {

            case true:

                $this->Cli()->Writeln("<info>{$key}: Config Added!</info>");

                $this->Cli()->Writeln("at ({$file})");

            break;


            case false:
            default:

                $this->Cli()->Writeln("<error>{$key}: Config Unavailable!</error>");

                break;

        }


        return null;

    }

    /**
     * Output for web
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */
    public function WebHandle($res)
    {
        return $res['msg'];
    }

}