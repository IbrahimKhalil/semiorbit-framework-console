<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Cache\Cache;
use Semiorbit\Cache\FrameworkCache;
use Semiorbit\Support\Str;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ModelBuilder;

class CacheClear extends Command
{


    public function Configure()
    {
        $this->Define("{-f} {--key}");
    }


    public function onStart()
    {

        if ($this->CliCommand() == 'ccc') $this->Option('key')->setValue('config');

        if ($this->CliCommand() == 'ccf' || $this->CliCommand() == 'ccc')

            $this->Flag('f')->setValue(true);

    }

    public function Execute()
    {


        $framework_only = boolval($this->Flag('f')->Value());

        $key = $this->Option('key')->Value();



        if ($framework_only && $key)

            $res = FrameworkCache::Clear($key);

        elseif ($framework_only)

            $res = FrameworkCache::Clear();

        elseif ($key)

            $res = Cache::Clear($key);

        else

            $res = Cache::Clear();


        return [$res, $key];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($result, $key) = $res;

        /** @var ModelBuilder $model */

        switch ($result) {

            case true:

                $this->Cli()->Writeln("<info>{$key} Cache Cleared!</info>");

            break;


            case false:
            default:

                $this->Cli()->Writeln("<error>{$key} Cache Clear Failed!</error>");

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