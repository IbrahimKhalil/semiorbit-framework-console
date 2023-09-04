<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ControllerBuilder;

class MakeController extends Command
{


    public function Configure()
    {
        $this->Define("{ctrl} {-a} {-b} {-c} {-r}");
    }


    public function Execute()
    {


        $ctrl_name = $this->Argument('ctrl')->Value();


        $rest = boolval($this->Flag('a')->Value()) ? 'Rest' : '';

        $base = boolval($this->Flag('b')->Value()) ? 'Base' : '';

        $clean = boolval($this->Flag('c')->Value());

        $default = $clean ? '' : null;

        $overwrite = boolval($this->Flag('r')->Value());



        $controller = new ControllerBuilder($ctrl_name, $rest ?: $base ?: $default);

        $result = $controller->Create($overwrite);


        return [$result, $controller];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($result, $controller) = $res;

        /** @var ControllerBuilder $controller */

        switch ($result) {

            case 200:

                $this->Cli()->Writeln("<info>({$controller->ClassName}): Controller Created</info>");

            break;


            case 400:

                $this->Cli()->Writeln("<error>({$controller->ClassName}): File Exists!</error>");

                break;

            case 300:
            default:

                $this->Cli()->Writeln("<error>({$controller->ClassName}): Failed!</error> Can not create controller file");

                break;

        }


        $this->Cli()->Writeln("at ({$controller->Path}:1)");


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