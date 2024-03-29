<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Cache\Cache;
use Semiorbit\Cache\FrameworkCache;
use Semiorbit\Session\Session;
use Semiorbit\Support\Str;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\GlobalVars;
use SemiorbitFwkLibrary\ModelBuilder;

class UseGlobals extends Command
{


    public function Configure()
    {
        $this->Define("{key} {value} {-c}");
    }


    public function Execute()
    {


        $clear = boolval($this->Flag('c')->Value());

        $key = $this->Argument('key')->Value();

        $value = $this->Argument('value')->Value();

//        try {
//
//
//
//        } catch (\Exception $exception) {
//
//            $this->Cli()->Writeln('<error>Invalid Arguments! Please enter:</error>');
//
//            $this->Cli()->Writeln('<comment>php sc use {key} {value}</comment>');
//
//            return [null, null, null];
//
//        }


        if ($clear)

            GlobalVars::Clear($key);

        else GlobalVars::Store($key, $value);


        return [$key, $value, $clear];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        [$key, $value, $clear] = $res;

        if ($clear)

            $this->Cli()->Writeln("<comment>{$key} Unused!</comment>");

        else if (!$key || $value === null)

            $this->Cli()->Writeln("<error>Invalid key or value</error>");

        else

            $this->Cli()->Writeln("<info>{$key}: {$value} Used!</info>");


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