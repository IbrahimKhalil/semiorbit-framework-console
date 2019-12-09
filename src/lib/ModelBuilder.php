<?php

namespace SemiorbitFwkLibrary;


use Semiorbit\Base\Application;
use Semiorbit\Component\Package;
use Semiorbit\Config\Config;
use Semiorbit\Db\DB;
use Semiorbit\Output\BasicTemplate;
use Semiorbit\Support\Str;

class ModelBuilder
{

    public $Name;

    public $Table;

    public $Namespace;

    public $FQName;

    public $FileExt;

    public $Path;

    public $FileName;

    public $Output;

    public $NamingCase;

    public $Package;


    const NAMING_PASCAL_CASE = 'p';

    const NAMING_CAMEL_CASE = 'c';

    const NAMING_SNAKE_CASE = 's';


    private static $_Definers = [];

    private $_UseDataType = false;


    public function __construct($name, $table = null, $naming_case = self::NAMING_PASCAL_CASE)
    {


        if (strstr($name, '::'))

            [$pkg, $name] = explode('::', $name, 2);


        $this->Name = Str::PascalCase($name);

        $this->Package = $pkg ?? GlobalVars::Read('pkg');

        if ($table === '1') $table = Str::SnakeCase($this->Name);

        $this->Table = $table;

        $this->FileExt = Config::StructureExtension(Config::GROUP__MODELS, Config::FrameworkConfig()[Config::GROUP__MODELS . '_ext']);

        $this->FileName = $this->Name . $this->FileExt;

        $this->Path = (($this->Package) ?

            Package::Select($this->Package)->ModelsPath() :

            Application::Service()->ModelsPath()) . $this->FileName;

        $this->Namespace = ($this->Package) ?

            Package::Select($this->Package)->ModelsNamespace() :

            'App';

        $this->NamingCase = $naming_case;

    }

    public function Create($overwrite = false, $update = false)
    {

        if ($overwrite || $update || !file_exists($this->Path)) {


            $template = BasicTemplate::From(__dir__ . '/templates/model.tpl');

            $template->With('CLASS_NAME', $this->Name)

                ->With('TABLE_NAME', $this->Table)

                ->With('MODEL_NS', $this->Namespace)

                ->With('FIELDS', $this->Table ? $this->GenerateFieldTemplate($this->Table) : false)

                ->With('USE_DATA_TYPE', $this->_UseDataType);


            $this->Output = $template->Render();


            return

                file_put_contents($this->Path, $this->Output) ? 200 : 300;

        } else return 400;

    }

    public static function RegisterDefiner(string $handle, callable $func)
    {
        self::$_Definers[$handle] = $func;
    }


    public static function UnregisterDefiner(string $handle)
    {
        if (isset(self::$_Definers[$handle]))

            unset(self::$_Definers[$handle]);
    }

    public static function ListDefiners()
    {
        return self::$_Definers;
    }


    public function GenerateFieldTemplate(string $table): array
    {

        static $ns = [];

        $fields = [];

        $tbl_description = DB::Table("DESCRIBE {$table}");


        while ($db_fld = $tbl_description->Read()) {


            $customFld = new ModelCustomFieldDefiner();

            $field = [];


            $field['FIELD_NAME'] = $db_fld['Field'];

            $field['NAME'] = $this->PrepareFieldName($db_fld);


            [$control, $props, $comment] = [null, [], null];


            foreach (self::ListDefiners() as $handle => $definer) {

                /**@var ModelCustomFieldDefiner $customFld */

                $customFld = call_user_func($definer, $db_fld);

                $control = $customFld->Control();

                $props = $customFld->Props();

                $comment = $customFld->Comment();

                if ($control) break;

            }

            if (!$control)

                [$control, $props, $comment] = $this->DefineField($db_fld);


            $field['CONTROL'] = $control;


            if (! $customFld->IsCustom()) {

                $props[] = $this->IsRequired($db_fld);

                $props[] = $this->DefaultValue($db_fld);

                $props[] = $this->IsReadOnly($db_fld);

            }

            $field['PROPS'] = implode('', $props);

            $field['COMMENT'] = $comment;



            $field['BASIC'] = !$customFld->IsCustom();

            $field['PROP_DEF'] = $customFld->PropDef();

            if (!in_array($customFld->Namespace(), $ns)) {

                $field['NS'] = $customFld->Namespace();

                $ns[] = $customFld->Namespace();

            }

            $field['DEFINE_FIELD_NAME'] = $customFld->DefineFieldName();



            $fields[$field['NAME']] = $field;

        }


        return $fields;

    }


    protected function PrepareFieldName($db_fld)
    {

        $field_name = $db_fld['Field'];

        switch ($this->NamingCase) {

            case self::NAMING_PASCAL_CASE:

                $name = Str::PascalCase($field_name);

                break;

            case self::NAMING_CAMEL_CASE:

                $name = Str::CamelCase($field_name);

                break;

            case self::NAMING_SNAKE_CASE:
            default:

                $name = Str::SnakeCase($field_name);

                break;

        }

        return $name;

    }


    protected function IsRequired($db_fld)
    {
        return ($db_fld['Null'] == 'NO') ? '->setRequired()' : '';
    }


    protected function DefaultValue($db_fld)
    {

        if (empty($db_fld['Default'])) {

            return '';

        } else {

            switch ($db_fld['Default']) {

                case 'CURRENT_TIMESTAMP':

                    return '->setDefaultValue( date("Y-m-d H:i:s") )';
                    break;

                default:

                    return "->setDefaultValue({$db_fld['Default']})";

            }
        }

    }


    protected function IsReadOnly($db_fld)
    {

        if ($db_fld['Default'] == 'CURRENT_TIMESTAMP'

            && $db_fld['Extra'] == 'on update CURRENT_TIMESTAMP')

            return '->setReadOnly()';

        return null;

    }


    public function ParseType($type)
    {


        $length = preg_match_all("#(\w+)\(?([\d ,]*)\)?#i", $type, $matches);

        $data_type = null;

        $unsigned = boolval(stristr($type, 'unsigned'));


        if ($length) {

            $data_type = $matches[1][0] ?? null;


            if ($length_str = $matches[2][0] ?? false) {

                if (strstr($length_str, ',')) {

                    $parts = explode(',', $length_str);

                    return [$data_type, intval($parts[0]), intval($parts[1]), $unsigned];

                } else {

                    return [$data_type, intval($length_str), null, $unsigned];

                }

            }

        }

        return [$data_type, null, null, $unsigned];


    }


    protected function DataType($data_type, $length)
    {

        $fwk_data_type = DataTypeMapper::Map($data_type, $length);

        $this->_UseDataType = true;

        return "->setType(DataType::{$fwk_data_type})";

    }

    protected function MaxLength($length)
    {
        return "->setMaxLength({$length})";
    }


    protected function DefineField($db_fld)
    {


        $props = [];

        $comment = '';

        [$data_type, $length, $decimals, $unsigned] = $this->ParseType($db_fld['Type']);


        // AUTO INCREMENT ID
        //==============================================================================================================

        if ($db_fld['Extra'] == 'auto_increment' && $db_fld['Key'] == 'PRI') {

            $control = 'ID';

            $props[] = '->setAutoIncrement(true)';

            if ($data_type != 'int')

                $this->DataType($data_type, $length);

        }


        // VARCHAR ID
        //==============================================================================================================

        elseif (in_array($db_fld['Type'], ['varchar(32)', 'varchar(13)', 'char(13)']) && $db_fld['Key'] == 'PRI') {

            $control = 'ID';

            $props[] = $this->DataType($data_type, $length);

        }


        // UUID_SHORT
        //==============================================================================================================

        elseif ($db_fld['Type'] == 'bigint(20) unsigned' && $db_fld['Key'] == 'PRI') {

            $control = 'ID';

            $props[] = "->IsUUID_SHORT()";

        }



        // VARCHAR FOREIGN KEY
        //==============================================================================================================

        elseif (in_array($db_fld['Type'], ['varchar(32)', 'varchar(13)', 'bigint(20) unsigned', 'char(13)'])) {

            $control = 'Select';

            $props[] = "->setForeignKey(, '{$db_fld['Field']}',)";

            if ($data_type !== 'bigint')

                $props[] = $this->DataType($data_type, $length);

        }



        // VARCHAR PIC
        //==============================================================================================================

        elseif ($db_fld['Type'] == 'varchar(5)') {

            $control = 'File';

            $props[] = "->setAutoResize(true)";

            $props[] = "->setThumbnail('cover', 309, 176)";

        }



        // VARCHAR TEXT
        //==============================================================================================================

        elseif ($data_type == 'varchar') {

            $control = 'Text';

        }



        // CHAR
        //==============================================================================================================

        elseif ($data_type == 'char') {

            $control = 'Text';

            $props[] = $this->DataType($data_type, $length);

        }



        // TEXT TEXTAREA
        //==============================================================================================================

        elseif (in_array($data_type, ['text', 'longtext', 'mediumtext', 'tinytext'])) {

            $control = 'TextArea';

        }



        // TEXT TEXTAREA - BLOB
        //==============================================================================================================

        elseif (in_array($data_type, ['blob', 'longblob', 'mediumblob'])) {

            $control = 'TextArea';

            $props[] = $this->DataType($data_type, $length);

        }



        // DATETIME
        //==============================================================================================================

        elseif ($data_type == 'datetime') {

            $control = 'DateTime';

            $props[] = $this->DataType($data_type, $length);

        }



        // TIMESTAMP
        //==============================================================================================================

        elseif ($data_type == 'timestamp') {

            $control = 'DateTime';

        }



        // DATE
        //==============================================================================================================

        elseif ($data_type == 'date') {

            $control = 'DateTime';

            $props[] = $this->DataType($data_type, $length);

            $props[] = '->setShowTime(false)';

            $props[] = '->setFormat("%Y-%m-%d")';

        }



        // FLOAT
        //==============================================================================================================

        elseif ($data_type == 'float') {

            $control = 'Number';

            $props[] = $this->DataType($data_type, $length);

        }


        // INT
        //==============================================================================================================

        elseif ($data_type == 'int') {

            $control = 'Number';

        }


        //DECIMAL TEXT
        //==============================================================================================================

        elseif ($data_type == 'decimal') {

            $control = 'Number';

            $props[] = $this->DataType($data_type, $length);

        }



        // TINYINT
        //==============================================================================================================

        elseif ($data_type == 'tinyint' && $length == 4) {

            $control = 'Select';

            $props[] = "->setOptions([0=>'', 1=>''])";

        }



        // SMALLINT
        //==============================================================================================================

        elseif ($data_type == "smallint") {

            $control = 'Select';

            $props[] = "->setForeignKey(, '{$db_fld['Field']}',)";

        }



        // UNSIGNED BIGINT(20)
        //==============================================================================================================

        elseif ($db_fld['Type'] == 'bigint(20) unsigned') {

            $control = 'Select';

            $props[] = "->setForeignKey(, '{$db_fld['Field']}',)";

        }


        // BIGINT
        //==============================================================================================================

        elseif ($data_type == 'bigint') {

            $control = 'Number';

        }



        //DOUBLE
        //==============================================================================================================

        elseif ($data_type == 'double') {

            $control = 'Number';

            $props[] = $this->DataType($data_type, $length);

        }



        //BOOL CHECKBOX
        //==============================================================================================================

        elseif ($db_fld['Type'] == 'tinyint(1)') {

            $control = 'Checkbox';

        }



        // Enum
        //==============================================================================================================

        elseif (substr($db_fld['Type'], 0, 4) == "enum") {

            $control = 'Select';

            $list = "'', " . substr($db_fld['Type'], 5, strlen($db_fld['Type']) - 6);

            $props[] = "->setOptions([{$list}])";

        }


        //UNKNOWN
        //==============================================================================================================

        else {

            $control = 'Text';

            $comment = "# UNSPECIFIED FIELD TEMPLATE OF TYPE ({$db_fld['Type']})";

        }


        if ($length) {

            $explicit =

                ($data_type == 'varchar' && $length != 255) ||

                ($data_type == 'decimal' && $length != 11) ||

                ($length != 11 && ($control == 'Number' && $control != 'ID')) ||

                ($length != 20 && ($control == 'ID' || $control == 'Select') && $data_type != 'enum');


            if ($explicit)

                $props[] = "->setMaxLength({$length})";


            if ($decimals && $control == 'Number')

                $props[] = "->NumberFormat({$decimals})";


        }


        if ($unsigned && ($control != 'Select' && $data_type == 'bigint'))

            $props[] = "->setUnsigned()";

        elseif (!$unsigned && ($control == 'Select' && $data_type == 'bigint'))

            $props[] = "->setUnsigned(false)";


        return [$control, $props, $comment];

    }


}