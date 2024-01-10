<?php


namespace SemiorbitFwkConsole;


use Semiorbit\Console\Command;
use SemiorbitFwkLibrary\SqlMigrate;
use SemiorbitFwkLibrary\Utils;

class MakeSqlFile extends Command
{


    public function Configure()
    {
        $this->Define("{table} {--con}");
    }


    public function Execute()
    {


        $table = $this->Argument('table')->Value();

        $con = Utils::ConnectionSelector($this);



        $files = [];

        if ($con && $table) {

            $migrate = new SqlMigrate($con);

            $files = $migrate->CreateSqlFile($table);



        }



        return [$files, [$con, $table]];


    }

    /**
     * Output for cli
     *
     * @param mixed $res Results or resources returned after Execute
     * @return mixed Output
     */

    public function CliHandle($res)
    {

        list($result, $con_tbl) = $res;



        switch (count($result) > 0) {

            case true:

                $this->Cli()->Writeln("<info>(`{$con_tbl[0]}:{$con_tbl[1]}`): Sql files created in:</info>");

                foreach ($result as $file) {

                    $this->Cli()->Writeln("<info>{$file}</info>");

                }

                break;


            case false:
            default:

                $this->Cli()->Writeln("<error>(`{$con_tbl[0]}:{$con_tbl[1]}`): Failed!</error> Can not create sql files");

                break;

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