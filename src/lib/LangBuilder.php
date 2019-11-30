<?php


namespace SemiorbitFwkLibrary;


use Semiorbit\Base\Application;
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




    public function __construct($lang, $table = null)
    {

        $this->Lang = $lang;

        $this->Table = $table;

        $this->FileExt = Config::StructureExtension(Config::GROUP__LANG, Config::FrameworkConfig()[Config::GROUP__LANG . '_ext']);

        $this->FileName = Str::ParamCase($table) . ".{$lang}" . $this->FileExt;

        $this->Path = Application::Service()->BasePath(Config::StructureDirectory(Config::GROUP__LANG, 'lang') . "/{$lang}/" . $this->FileName) ;


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

        $formatted_str = preg_replace("/(_)(en|ar|tr|fr|so|no)$/", " ($2)", $formatted_str);


        return $formatted_str;
    }




}