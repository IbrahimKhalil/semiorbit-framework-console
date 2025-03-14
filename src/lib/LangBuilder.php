<?php


namespace SemiorbitFwkLibrary;


use Semiorbit\Base\Application;
use Semiorbit\Component\Package;
use Semiorbit\Config\Config;
use Semiorbit\Db\DB;
use Semiorbit\Output\BasicTemplate;
use Semiorbit\Support\Str;

class LangBuilder
{

    public $Lang;

    public $Table;

    public $FileExt;

    public $Path;

    public $FileName;

    public $Output;

    public $Package;

    public $LangDir;


    public function __construct($name, $lang, $table = null, $con = null)
    {

        if ($con) DB::UseConnection($con);


        if (strstr($name, '::'))

            [$pkg, $name] = explode('::', $name, 2);


        $this->Lang = $lang;

        $this->Table = $table;

        $this->Package = $pkg ?? GlobalVars::Read('pkg');

        $this->FileExt = Config::StructureExtension(Config::GROUP__LANG, Config::FrameworkConfig()[Config::GROUP__LANG . '_ext']);

        $this->FileName = Str::ParamCase($name) . ".{$lang}" . $this->FileExt;


        $this->LangDir = (($this->Package) ?

            Package::Select($this->Package)->LangPath() . "{$lang}/" :

            Application::Service()->BasePath(Config::StructureDirectory(Config::GROUP__LANG, 'lang') . "/{$lang}/"));


        if (!file_exists($this->LangDir))

            mkdir($this->LangDir, 0777, true);


        $this->Path = $this->LangDir . $this->FileName;

    }


    public function Create($overwrite = false, $update = false)
    {

        if ($overwrite || $update || !file_exists($this->Path)) {


            $template = BasicTemplate::From(__dir__ . '/templates/lang.tpl');

            $template->With('INDEX_TITLE', Str::PascalCase($this->Table, ' '));

            $template->With('FIELDS', $this->Table ? $this->GenerateFieldTemplate($this->Table) : false);


            $this->Output = $template->Render();


            return

                file_put_contents($this->Path, $this->Output) ? 200 : 300;

        } else return 400;

    }


    public function GenerateFieldTemplate(string $table): array
    {

        $fields = [];

        $tbl_description = DB::Table("DESCRIBE {$table}");


        $field = [];

        while ($db_fld = $tbl_description->Read()) {


            $field['NAME'] = $db_fld['Field'];

            $field['TRANS'] = static::FormatValue($field['NAME']);


            $fields[$field['NAME']] = $field;

        }


        return $fields;

    }


    public static function FormatValue($name)
    {

        $formatted_str = Str::PascalCaseKeepLang($name);

        $formatted_str = Str::SplitByCaps($formatted_str, " ");

        $list_lang = implode('|', Config::Languages());

        $formatted_str = preg_replace("/(_)(".$list_lang.")$/", " ($2)", $formatted_str);


        return $formatted_str;
    }


    public static function CopyLang($lang, $to_lang, $pkg = null, $overwrite = false)
    {

        $pkg = $pkg ?? GlobalVars::Read('pkg');

        $lang_dir = (($pkg) ?

            Package::Select($pkg)->LangPath() . "{$lang}/" :

            Application::Service()->BasePath(Config::StructureDirectory(Config::GROUP__LANG, 'lang') . "/{$lang}/"));


        if (!file_exists($lang_dir)) return false;


        $to_lang_dir = (($pkg) ?

            Package::Select($pkg)->LangPath() . "{$to_lang}/" :

            Application::Service()->BasePath(Config::StructureDirectory(Config::GROUP__LANG, 'lang') . "/{$to_lang}/"));


        if (!file_exists($to_lang_dir)) mkdir($to_lang_dir, 0777, true);


        if ($files = scandir($lang_dir, SCANDIR_SORT_NONE)) {

            foreach ($files as $file) {

                if (ends_with($file, '.inc')) {

                    $dest = $to_lang_dir . str_replace(".{$lang}.inc", ".{$to_lang}.inc", $file);

                    if ($overwrite == false && file_exists($dest)) continue;

                    if (!copy($lang_dir . $file, $dest))

                        return false;

                }

            }


        } else return false;


        return true;

    }



}