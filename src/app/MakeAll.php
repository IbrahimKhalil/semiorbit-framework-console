<?php


namespace SemiorbitFwkConsole;



use Semiorbit\Config\Config;
use Semiorbit\Support\Str;
use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\ControllerBuilder;
use SemiorbitFwkLibrary\ModelBuilder;

class MakeAll extends Command
{


    public function Configure()
    {
        $this->Define("{name} {-r}");
    }


    public function Execute()
    {


        $name = $this->Argument('name')->Value();

        $overwrite = boolval($this->Flag('r')->Value());



        $model = new ModelBuilder($name, '1');

        $result = $model->Create($overwrite);

        $model_res = [$result, $model];



        $controller = new ControllerBuilder($name);

        $result = $controller->Create($overwrite);


        $ctrl_result = [$result, $controller];


        return [$model_res, $ctrl_result];



    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        [$model_res, $ctrl_result] = $res;


        (new MakeModel())->CliHandle($model_res);

        (new MakeController())->CliHandle($ctrl_result);


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