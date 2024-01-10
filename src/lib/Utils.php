<?php

namespace SemiorbitFwkLibrary;


use Semiorbit\Config\Config;
use Semiorbit\Console\Command;
use Semiorbit\Db\DB;

class Utils
{

    public static function ConnectionSelector(Command $command)
    {

        $con = $command->Option('con')->Value();


        $config = Config::DbConnections();

        $con_list = array_keys($config);

        if (! $con) {


            if (count($config) === 1) {

                $con = 0;

            } else {

                SELECT_CON:

                $con = $command->Cli()->Choice('Select connection:', $con_list);

            }

        }



        if (isset($con_list[$con]) && !isset($config[$con])) $con = $con_list[$con];

        else if (!isset($config[$con])) goto SELECT_CON;


        return $con;


    }


    public static function SqlFilePath($con, $query_fn)
    {

        $sql = __DIR__ . '/sql/' . (DB::UseConnection($con)->Driver()->DriverManagementSystem()) . '/' . $query_fn . '.sql';

        if (! file_exists($sql))

            $sql = __DIR__ . '/sql/generic/' . $query_fn . '.sql';

        return $sql;

    }

}