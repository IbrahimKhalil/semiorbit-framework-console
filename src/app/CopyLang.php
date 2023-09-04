<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Cache\Cache;
use Semiorbit\Cache\FrameworkCache;
use Semiorbit\Support\Str;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\GlobalVars;
use SemiorbitFwkLibrary\LangBuilder;
use SemiorbitFwkLibrary\ModelBuilder;

class CopyLang extends Command
{

    //TODO: https://translate.googleapis.com/translate_a/single?client=gtx&sl={0}&tl={1}&dt=t&dt=rm&dj=1&q={2}


    public function Configure()
    {
        $this->Define("{lng} {to_lng*} {-r} {--pkg}");
    }



    public function Execute()
    {

        $lng = $this->Argument('lng')->Value();

        $to_lngs = (array) $this->Argument('to_lng')->Value();



        $overwrite = boolval($this->Flag('r')->Value());

        $pkg = $this->Option('pkg')->Value() ?: GlobalVars::Read('pkg');


        $confirm = $this->Cli()->Confirm("Package: {$pkg} - Copy {$lng} Files to " . implode(', ' , $to_lngs) . ($overwrite ? " OVERWRITE is enabled" : "") . "?");

        $res = false;

        if ($confirm === 'y') {

            foreach ($to_lngs as $to_lng) {

                $res = LangBuilder::CopyLang($lng, $to_lng, $pkg, $overwrite);

                $this->Cli()->Writeln($res ?

                    "<info>{$to_lng} Success</info>" :

                    "<error>{$to_lng} Failed</error>");

            }


        }


        return [$res, $lng];

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

                $this->Cli()->Writeln("<info>{$key} Lang Copied!</info>");

            break;


            case false:
            default:

                $this->Cli()->Writeln("<error>{$key} Lang Copy Failed!</error>");

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