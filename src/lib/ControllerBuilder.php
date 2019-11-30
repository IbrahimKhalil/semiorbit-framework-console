<?php


namespace SemiorbitFwkLibrary;


use Semiorbit\Base\Application;
use Semiorbit\Component\Finder;
use Semiorbit\Component\Package;
use Semiorbit\Config\Config;
use Semiorbit\Data\DataSet;
use Semiorbit\Db\DB;
use Semiorbit\Output\BasicTemplate;
use Semiorbit\Support\Str;

class ControllerBuilder
{

    public $Name;

    public $DataSet;

    public $DataSetNs;

    public $Namespace;

    public $FQName;

    public $FileExt;

    public $Path;

    public $FileName;

    public $Output;

    public $Package;

    public $Type;

    public $ClassName;




    public function __construct($name, $type = null)
    {


        if (strstr($name, '::'))

            [$pkg, $name] = explode('::', $name, 2);


        if ($type === null) $type = (Config::ApiMode() ? 'Rest' : 'Base');


        $this->Name = Str::PascalCase($name);

        $this->ClassName = $this->Name . Config::ControllerSuffix();

        $this->Package = $pkg ?? GlobalVars::Read('pkg');

        $this->Type = $type;


        $ds_path = (($this->Package) ?

                Package::Select($this->Package)->ModelsPath() :

                Application::Service()->ModelsPath()) .

                ($this->Name .

                Config::StructureExtension(Config::GROUP__MODELS, Config::FrameworkConfig()[Config::GROUP__MODELS . '_ext']));


        if (file_exists($ds_path)) {

            $this->DataSet = $this->Name;

            $this->DataSetNs = (($this->Package) ?

                Package::Select($this->Package)->ModelsNamespace() :

                'App') . '\\' . $this->Name;

        }


        $this->FileExt = Config::StructureExtension(Config::GROUP__CONTROLLERS, Config::FrameworkConfig()[Config::GROUP__CONTROLLERS . '_ext']);

        $this->FileName = $this->ClassName . $this->FileExt;

        $this->Path = (($this->Package) ?

            Package::Select($this->Package)->ControllersPath() :

            Application::Service()->ModelsPath()) . $this->FileName;

        $this->Namespace = ($this->Package) ?

            Package::Select($this->Package)->ControllersNamespace() :

            'App\\Http';


    }

    public function Create($overwrite = false)
    {

        if ($overwrite || !file_exists($this->Path)) {


            $template = BasicTemplate::From(__dir__ . '/templates/controller.tpl');

            $template->With('CLASS_NAME', $this->ClassName)

                ->With('NAME', $this->Name)

                ->With('CTRL_NS', $this->Namespace)

                ->With('CTRL_TYPE', $this->Type)

                ->With('PACKAGE', $this->Package)

                ->With('DATASET', $this->DataSet)

                ->With('DATASET_NS', $this->DataSetNs)

                ->With('DEFAULT', $this->Type === '')

                ->With('BASE', $this->Type === 'Base')

                ->With('REST', $this->Type === 'Rest');


            $this->Output = $template->Render();


            return

                file_put_contents($this->Path, $this->Output) ? 200 : 300;

        } else return 400;

    }


}