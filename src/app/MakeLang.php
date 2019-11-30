<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Config\Config;
use Semiorbit\Support\Str;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\LangBuilder;


class MakeLang extends Command
{


    public function Configure()
    {
        $this->Define("{dict} {--table=1} {--lang=0} {-c} {-u} {-r}");
    }


    public function Execute()
    {


        $dict_name = $this->Argument('dict')->Value();

        $clean = boolval($this->Flag('c')->Value());

        $update = boolval($this->Flag('u')->Value());

        $overwrite = boolval($this->Flag('r')->Value());

        $table = $this->Option('table')->Value();

        if ($table === '1') $table = Str::SnakeCase($dict_name);

        $lang = $this->Option('lang')->Value();

        $lang = ($lang == 0) ? Config::Languages() : (array) $lang;


        foreach ($lang as $lang_code) {

            $files[$lang_code] = new LangBuilder($lang_code, $table);

            $result[$lang_code] = ($files[$lang_code])->Create($overwrite, $update);

        }



        return [$result, $files];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($result, $files) = $res;


        foreach ($files as $lang => $file) {

            /** @var LangBuilder $file */


            switch ($result) {

                case 200:

                    $this->Cli()->Writeln("<info>({$lang}): Lang Created</info>");

                    break;


                case 400:

                    $this->Cli()->Writeln("<error>({$lang}): File Exists!</error>");

                    break;

                case 300:
                default:

                    $this->Cli()->Writeln("<error>({$lang}): Failed!</error> Can not create lang file");

                    break;

            }


            $this->Cli()->Writeln("at ({$file->Path}:1)");

        }

        //$this->Cli()->Writeln( "at App\Http\LoginCtrl.Index(E:\Projects\www\digitalschool\system\api\src\app\Http\LoginCtrl.php:19)");

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