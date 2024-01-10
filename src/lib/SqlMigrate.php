<?php

namespace SemiorbitFwkLibrary;


use DateTime;
use DateTimeZone;
use Semiorbit\Base\Application;
use Semiorbit\Db\DB;

class SqlMigrate
{

    public $Connection;

    public $Table;

    private $_SqlPath = '';


    public function __construct($con)
    {
        $this->Connection = $con;
    }


    public function SqlPath(): string
    {
        return $this->_SqlPath ?: $this->_SqlPath = Application::DatabasePath('sql/' . $this->Connection) . '/';
    }

    public function CreateSqlFile($table): array
    {


        if (! is_dir($this->SqlPath()  . "rollback")) {

            // Create the directories 'database/{con}' and 'database/{con}/rollback'

            mkdir($this->SqlPath() . 'rollback', 0777, true);

        }


        // Create a DateTime object for the current date and time in UTC

        $dateTimeUtc = new DateTime('now', new DateTimeZone('UTC'));


        // Format the DateTime object

        $dt = $dateTimeUtc->format("Y_m_d_His_");

        $sql_fn = $this->SqlPath() . $dt . $table . '.sql';

        $sql_rollback_fn = $this->SqlPath() . 'rollback/' . $dt . $table . '.sql';

        file_put_contents($sql_fn,  '');

        file_put_contents($sql_rollback_fn,  '');


        return [$sql_fn, $sql_rollback_fn];

    }

    public function Up($file, $batch)
    {



            $filename_without_ext = pathinfo($file, PATHINFO_FILENAME);

            // Check filename in migrations table

            $sql_exists = file_get_contents(__DIR__ . '/sql/generic/migration_exists.sql');

            $sql_exists = str_replace('?', "\"{$filename_without_ext}\"", $sql_exists);




            if (!DB::WithConnection($this->Connection)->Find($sql_exists)) {


                $sql_insert = file_get_contents(

                    __DIR__ . '/sql/generic/migration_insert.sql'

                );

                $sql_insert = str_replace('@migration', "\"{$filename_without_ext}\"", $sql_insert);

                $sql_insert = str_replace('@batch', "{$batch}", $sql_insert);

                if (DB::WithConnection($this->Connection)->Execute($sql_insert)) {

                    if ($sql = file_get_contents($this->_SqlPath . $file)) {

                        return DB::WithConnection($this->Connection)->ExecuteAll($sql);

                    }

                } else {

                    // Don't continue

                    return false;

                }


            }


        return null;

    }


    public function Down($file)
    {


        // 1- Get rollback file path

        $fn = $this->SqlPath() . 'rollback/' . $file . '.sql';


        // 2- If not exists or empty return false

        if (! file_exists($fn)) return false;

        $rollback_sql = file_get_contents($fn);

        if (! $rollback_sql) return false;


        // 3- Execute it

        $res = DB::ExecuteAll($rollback_sql);

        if ($res !== false) {

            // 4- Success: then remove row from migrations table

            $rem_sql = file_get_contents(

                Utils::SqlFilePath($this->Connection, 'migration_rem')

            );

            $rem_sql = str_replace('@file', "\"{$file}\"", $rem_sql);

            DB::Execute($rem_sql);

            return $res;

        } else {

            // 5- Failed: return false

            return false;

        }

    }


    public function CreateMigrationsTable()
    {

        $sql = file_get_contents(

            Utils::SqlFilePath($this->Connection, 'create_migrations')

        );

        DB::WithConnection($this->Connection)->ExecuteAll($sql);

    }


}
