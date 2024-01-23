<?php


namespace SemiorbitFwkConsole;


use Semiorbit\Config\Config;
use Semiorbit\Console\Command;
use Semiorbit\Db\DB;
use SemiorbitFwkLibrary\SqlMigrate;
use SemiorbitFwkLibrary\Utils;

class MigrateSql extends Command
{


    public function Configure()
    {
        $this->Define("{-r} {--con}");
    }


    public function Execute()
    {

        $rollback = boolval($this->Flag('r')->Value());

        $con = Utils::ConnectionSelector($this);


        $confirm = $this->Cli()->Confirm('Do you really want to continue ' . ($rollback ? 'rollback' : 'upgrading') . ": '{$con}'?");

        if ($confirm !== 'y') exit();


        $migrated = [];

        $err = '';


        if ($con) {

            $migrate = new SqlMigrate($con);

            $migrate->CreateMigrationsTable();


            // Batch number

            $max_batch_sql = file_get_contents(__DIR__ . '/../lib/sql/generic/migration_max_batch.sql');

            if (($max_batch = DB::Find($max_batch_sql)) !== false) {

                $batch = $rollback ? intval($max_batch) : intval($max_batch) + 1;

            } else return [false, 'ERR', $con, $rollback];


            if ($rollback) {

                // 1- Load migrations last batch in DESC order

                $last_batch_sql = file_get_contents(__DIR__ . '/../lib/sql/generic/migration_last_batch_rows.sql');

                if ($batch = DB::Table($last_batch_sql)) {

                    // 2- Iterate batch rows

                    while ($row = $batch->Row()) {

                        // 3- Down rollback files

                        $res = $migrate->Down($row['migration']);

                        if ($res !== false && $res !== null) {

                            $res_line = 'Rollback: ' . $row['migration'] . " [result: ". json_encode($res, true) ."]";

                            $migrated[] = $res_line;

                            $this->Cli()->Writeln("<info>{$res_line}</info>");

                        } else if ($res === false) {

                            $err = $row['migration'];

                        }

                    }

                } else return [false, 'BATCH ERR:' . $batch, $con, $rollback];



            } else {

                $files = scandir($migrate->SqlPath());

                $files = array_diff($files, ['.', '..', 'rollback']);


                // Sort the files by filename in natural order

                sort($files);

                foreach ($files as $file) {

                    $res = $migrate->Up($file, $batch);



                    if ($res !== false && $res !== null) {

                        $res_line = $file . " [result: " . json_encode($res) . "]";

                        $migrated[] = $res_line;

                        $this->Cli()->Writeln("<info>{$res_line}</info>");

                    } else if ($res === false) {

                        $res_line = $file . " [Failed: " . print_r($res, true) . "]";

                        $this->Cli()->Writeln("<error>{$res_line}</error>");

                        $err = $file;

                        break;

                    }


                }

            }

        }



        return [count($migrated), $err, $con, $rollback];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($migrated, $err, $con, $rollback) = $res;

        if ($rollback) {

            $this->Cli()->Writeln("<info>Successfuly rollbacked [{$migrated}] sql files from '{$con}'</info>");

        } else {

            $this->Cli()->Writeln("<info>Successfuly migrated [{$migrated}] sql files to '{$con}'</info>");

        }


        if ($err) {

            if ($rollback) {

                $this->Cli()->Writeln("<error>(`Rollback of {$con}`): Failed! @ </error> ");

            }  else {

                $this->Cli()->Writeln("<error>(`Migration of {$con}`): Failed! @ </error> ");

            }

            $this->Cli()->Writeln("<error>{$err}</error> ");


        }


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