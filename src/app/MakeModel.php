<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ModelBuilder;
use SemiorbitFwkLibrary\Utils;

class MakeModel extends Command
{


    public function Configure()
    {
        $this->Define("{model} {--table=1} {-c} {-u} {--case=p} {-r} {--con}");
    }


    public function Execute()
    {


        $model_name = $this->Argument('model')->Value();


        $clean = boolval($this->Flag('c')->Value());

        $update = boolval($this->Flag('u')->Value());

        $overwrite = boolval($this->Flag('r')->Value());

        $table = $this->Option('table')->Value();

        $naming_case = $this->Option('case')->Value();


        $con = Utils::ConnectionSelector($this);


        $model = new ModelBuilder($model_name, $clean ? null : $table, $naming_case, $con);

        $result = $model->Create($overwrite, $update);


        return [$result, $model];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($result, $model) = $res;

        /** @var ModelBuilder $model */

        switch ($result) {

            case 200:

                $this->Cli()->Writeln("<info>({$model->Name}): Model Created</info>");

            break;


            case 400:

                $this->Cli()->Writeln("<error>({$model->Name}): File Exists!</error>");

                break;

            case 300:
            default:

                $this->Cli()->Writeln("<error>({$model->Name}): Failed!</error> Can not create model file");

                break;

        }


        $this->Cli()->Writeln("at ({$model->Path}:1)");


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