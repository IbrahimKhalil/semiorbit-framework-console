<?php


namespace SemiorbitFwkLibrary;


class DataTypeMapper
{

    public static function Map($field_type, $length, $database = 'mysql')
    {
        return static::MapMysql($field_type, $length);
    }

    public static function MapMysql($field_type, $length = null)
    {

        switch ($field_type) {

            case 'char':

                $data_type = 'CHAR';

                break;

            case 'tinyint':

                $data_type = $length === 1 ? 'Bool' : 'Int';

                break;

            case 'int':
            case 'bigint':
            case 'smallint':
            case 'mediumint':
            case 'year':

                $data_type = 'INT';

                break;


            case 'text':
            case 'longtext':
            case 'mediumtext':
            case 'tinytext':

                $data_type = 'TEXT';

                break;


            case  'double':
            case 'float':
            case 'timestamp':
            case 'datetime':
            case 'date':
            case 'time':
            case 'decimal':

                $data_type = strtoupper($field_type);

                break;


            case 'binary':
            case 'varbinary':
            case 'blob':
            case 'tinyblob':
            case 'longblob':
            case 'mediumblob':

                $data_type = 'BINARY';

                break;

            case 'varchar':
            case 'enum':
            default:

                $data_type = 'VARCHAR';

                break;

        }

        return $data_type;

    }

}