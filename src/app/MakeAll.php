<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Config\Config;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ControllerBuilder;
use SemiorbitFwkLibrary\ModelBuilder;
use SemiorbitFwkLibrary\Utils;

class MakeAll extends Command
{


    public function Configure()
    {
        $this->Define("{name} {--table=1} {--case=p} {-r} {--con}");
    }


    public function Execute()
    {


        $name = $this->Argument('name')->Value();

        $overwrite = boolval($this->Flag('r')->Value());


        $table = $this->Option('table')->Value();

        $naming_case = $this->Option('case')->Value();

        $con = Utils::ConnectionSelector($this);


        $model = new ModelBuilder($name, $table, $naming_case, $con);

        $result = $model->Create($overwrite);

        $model_res = [$result, $model];



        if ((Config::ApiMode() || Config::ApiControllersDir())) {

            $controller = new ControllerBuilder($name, 'Rest');

            $result = $controller->Create($overwrite);


            $rest_ctrl_result = [$result, $controller];

        }

        if (! Config::ApiMode()) {

            $controller = new ControllerBuilder($name, 'Base');

            $result = $controller->Create($overwrite);


            $ctrl_result = [$result, $controller];

        }


        return [$model_res, $ctrl_result ?? [], $rest_ctrl_result ?? []];



    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        [$model_res, $ctrl_result, $rest_ctrl_result] = $res;


        (new MakeModel())->CliHandle($model_res);

        if ($ctrl_result) (new MakeController())->CliHandle($ctrl_result);

        if ($rest_ctrl_result) (new MakeController())->CliHandle($rest_ctrl_result);


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