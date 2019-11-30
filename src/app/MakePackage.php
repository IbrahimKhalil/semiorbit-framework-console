<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Base\Application;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ModelBuilder;
use SemiorbitFwkLibrary\PackageBuilder;

class MakePackage extends Command
{


    public function Configure()
    {
        $this->Define("{package} {-r} {-f}");
    }



    public function Execute()
    {


        $package = $this->Argument('package')->Value();

        $overwrite = boolval($this->Flag('r')->Value());

        $all_folders = boolval($this->Flag('f')->Value());


        $pkg = new PackageBuilder($package);

        $result = $pkg->Create($overwrite, $all_folders);

        $registered = $pkg->Register();


        return [$result, $pkg->Package, $pkg->ProviderPath, $registered, Application::Service()->ConfigPath('services.inc')];


    }


    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        [$result, $key, $file, $registered, $services] = $res;

        /** @var ModelBuilder $model */

        switch ($result) {

            case true:

                $this->Cli()->Writeln("<info>{$key}: Package Added!</info>");

                $this->Cli()->Writeln("at ({$file})");

            break;


            case false:
            default:

                $this->Cli()->Writeln("<error>{$key}: Package Failed!</error>");

                break;

        }


        if ($registered) {

            $this->Cli()->Writeln("<info>{$key}: Package Registered!</info>");

            $this->Cli()->Writeln("at ({$services})");

        } else {

            $this->Cli()->Writeln("<error>Unable to Register Package ({$key})!</error>");

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