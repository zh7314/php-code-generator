<?php

namespace ZX\Tool;

use ZX\Tool\Mysql;

class MysqlOperation
{

    public static $conn = null;
    public static $config = null;

    public static function setConnection(array $param = [])
    {
        self::$config = $param;
        self::$conn = Mysql::getInstance(self::$config);
    }


    public static function getAllTableName()
    {
        $dbname = self::$config['dbname'];
        return self::$conn->fetchAll("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '{$dbname}'");
    }

    public static function getTableColumn(string $tableName, $allColumn = false)
    {
        $dbname = self::$config['dbname'];
        if ($allColumn) {
            return self::$conn->fetchAll("SELECT * FROM information_schema.columns WHERE table_schema = '{$dbname}' AND TABLE_NAME = '{$tableName}'");
        } else {
            return self::$conn->fetchAll("SELECT COLUMN_NAME,DATA_TYPE,TABLE_SCHEMA,TABLE_NAME,COLUMN_COMMENT FROM information_schema.columns WHERE table_schema = '{$dbname}' AND TABLE_NAME = '{$tableName}'");
        }
    }

    //mysql转成PHP规则
    public static function transforColumnMysqlToPHP(array $column)
    {
        $php = [];

        foreach ($column as $k => $v) {
            //列名
            $php[$k]['COLUMN_NAME'] = $v['COLUMN_NAME'];
            //列数据类型
            $php[$k]['DATA_TYPE'] = self::transforColumnRule($v['DATA_TYPE']);
            //库名
            $php[$k]['TABLE_SCHEMA'] = $v['TABLE_SCHEMA'];
            //表名
            $php[$k]['TABLE_NAME'] = $v['TABLE_NAME'];
            //注释
            $php[$k]['COLUMN_COMMENT'] = $v['COLUMN_COMMENT'];
        }

        return $php;
    }

    //转换规则
    public static function transforColumnRule(string $type)
    {
        if ($type == 'varchar') {
            return 'string';
        } elseif ($type == 'char') {
            return 'string';
        } elseif ($type == 'text') {
            return 'string';
        } elseif ($type == 'int') {
            return 'int';
        } elseif ($type == 'tinyint') {
            return 'int';
        } elseif ($type == 'bigint') {
            return 'float';
        } elseif ($type == 'date') {
            return 'string';
        } elseif ($type == 'datetime') {
            return 'string';
        } elseif ($type == 'time') {
            return 'string';
        } elseif ($type == 'decimal') {
            return 'float';
        } elseif ($type == 'json') {
            return 'string';
        } elseif ($type == 'longtext') {
            return 'string';
        } elseif ($type == 'boolean') {
            return 'bool';
        } else {
            return 'string';
        }
    }

    //转换规则
    public static function getdefaultValue(string $type)
    {
        $defaultValue = [];
        $defaultValue['string'] = "''";
        $defaultValue['float'] = 0;
        $defaultValue['int'] = 0;
        $defaultValue['array'] = "[]";
        $defaultValue['bool'] = 'true';

        return $defaultValue[$type];
    }

    public static function transforColumnMysqlToGolang(array $column)
    {
        $php = [];

        foreach ($column as $k => $v) {
            //列名
            $php[$k]['COLUMN_NAME'] = $v['COLUMN_NAME'];
            //列数据类型
            $php[$k]['DATA_TYPE'] = self::transforColumnRuleToGolang($v['DATA_TYPE']);
            //库名
            $php[$k]['TABLE_SCHEMA'] = $v['TABLE_SCHEMA'];
            //表名
            $php[$k]['TABLE_NAME'] = $v['TABLE_NAME'];
            //注释
            $php[$k]['COLUMN_COMMENT'] = $v['COLUMN_COMMENT'];
        }

        return $php;
    }

    //转换规则
//    public static function transforColumnRuleToGolang(string $type)
//    {
//        if ($type == 'varchar') {
//            return 'string';
//        } elseif ($type == 'char') {
//            return 'string';
//        } elseif ($type == 'text') {
//            return 'string';
//        } elseif ($type == 'int') {
//            return 'int';
//        } elseif ($type == 'tinyint') {
//            return 'int';
//        } elseif ($type == 'bigint') {
//            return 'float';
//        } elseif ($type == 'date') {
//            return 'string';
//        } elseif ($type == 'datetime') {
//            return 'string';
//        } elseif ($type == 'time') {
//            return 'string';
//        } elseif ($type == 'decimal') {
//            return 'float';
//        } elseif ($type == 'json') {
//            return 'string';
//        } elseif ($type == 'longtext') {
//            return 'string';
//        } elseif ($type == 'boolean') {
//            return 'bool';
//        } else {
//            return 'string';
//        }
//    }
//
//    //转换规则
//    public static function getdefaultValueToGolang(string $type)
//    {
//        $defaultValue = [];
//        $defaultValue['string'] = "\"\"";
//        $defaultValue['float'] = 0.00;
//        $defaultValue['int'] = 0;
//        $defaultValue['array'] = "[]";
//        $defaultValue['bool'] = 'true';
//
//        return $defaultValue[$type];
//    }
}
