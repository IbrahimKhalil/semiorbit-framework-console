<?php


namespace SemiorbitFwkLibrary;


use Semiorbit\Base\Application;
use Semiorbit\Cache\FrameworkCache;
use Semiorbit\Config\Config;
use Semiorbit\Output\BasicTemplate;
use Semiorbit\Support\Path;
use Semiorbit\Support\Str;

class PackageBuilder
{

    public $Package;

    public $Path;

    public $Output;

    public $PackageID;

    public $ProviderPath;

    public $Namespace;


    public function __construct($package)
    {

        if (strstr($package, "/")) {

            $parts = explode("/", $package);

            $last = count($parts) - 1;

            $parts[$last] = Str::PascalCase($parts[$last]);

            $package = $parts[$last];

            $path = implode("/", $parts);

            $ns = "App\\" . implode("\\", $parts);

        }

        $this->Package = Str::PascalCase($package);

        $this->Path = Application::Service()->AppPath($path ?? $this->Package);

        $this->PackageID = Str::SnakeCase($this->Package);

        $this->ProviderPath = $this->Path . "/{$this->Package}ServiceProvider.php";

        $this->Namespace = $ns ?? "App\\{$this->Package}";

    }

    public function Create($overwrite = false, $all_folders = false)
    {


        if ($overwrite || !file_exists($this->Path)) {


            if ($this->CreateStructure($all_folders)) {

                $template = BasicTemplate::From(__dir__ . '/templates/package.tpl');

                $template->With('PKG_ID', $this->PackageID)

                    ->With('PKG', $this->Package)

                    ->With('PKG_NS', $this->Namespace);


                $this->Output = $template->Render();


                return

                    file_put_contents($this->ProviderPath, $this->Output);

            }

        }

        return false;

    }



    public function Register()
    {

        FrameworkCache::Clear();

        $registered = false;

        $services = Config::Services();

        $prep_services = [];

        if (! in_array($cur = "{$this->Namespace}\\{$this->Package}ServiceProvider", $services)) {

            $services[] = $cur;

            foreach ($services as $service_fq) {

                $pos = (strrpos($service_fq, '\\'));

                $prep_services[] = ['SERVICE' => substr($service_fq,  $pos ? $pos + 1 : 0), 'SERVICE_NS' => $service_fq];

            }


            $template = BasicTemplate::From(__dir__ . '/templates/services.tpl');

            $template->With('SERVICES', $prep_services);

            $output = $template->Render();

            $registered = file_put_contents(Application::Service()->ConfigPath('services.inc'), $output);

        }

        return $registered;

    }

    public function CreateStructure($all_folders = false)
    {

        if ($res = mkdir($this->Path)) {

            mkdir($this->Path . '/Http');

            mkdir($this->Path . '/routes');


            if ($all_folders) {

                mkdir($this->Path . '/lang');

                mkdir($this->Path . '/views');

                foreach (Config::Languages() as $language)

                    mkdir($this->Path . '/lang/' . $language);

            }


        }

        return $res;

    }




}