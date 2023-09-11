<?php

namespace ZX\Generator;

use ZX\Hump;
use ZX\MysqlOperation;
use ZX\File;
use ZX\BaseGenerator;

class WebmanSnake extends BaseGenerator
{

    public static function getClassName()
    {
        return basename(__CLASS__);
    }

    public static function generatorAllTable()
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $k => $v) {
                self::generatorTable($v['TABLE_NAME']);
            }
        }
    }

    public static function generatorTable(string $tableName)
    {
        //使用前手动删除生成的app
        $column = MysqlOperation::getTableColumn($tableName);
        //生成目录
        File::generatorPath();
        //生产控制器
        self::generatorController($tableName, $column);
        //生产服务
        self::generatorService($tableName, $column);
        //生产模型
        self::generatorModel($tableName, $column);
    }

    public static function generatorController(string $tableName, array $column)
    {
        //表名
        $upTableName = ucfirst(Hump::camelize($tableName));
        //列数据
        $paramString = self::generatorParamString($column);

        $content = File::getFileContent(false, 'Controller.template', self::getClassName());

        $search = ['{upTableName}', '{paramString}'];
        $replace = [$upTableName, $paramString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . 'Controller.php', self::$allControllerPath, $contents);
    }

    public static function generatorService(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));
        $lcTableName = lcfirst($upTableName);

        $paramString = self::generatorParamServiceString($tableName, $column);

        $content = File::getFileContent(false, 'Service.template', self::getClassName());

        $ifParamString = self::generatorIfParamServiceString($tableName, $column);

        $search = ['{upTableName}', '{paramString}', '{lcTableName}', '{ifParamString}'];
        $replace = [$upTableName, $paramString, $lcTableName, $ifParamString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . 'Service.php', self::$allServicePath, $contents);
    }

    public static function generatorModel(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(false, 'Model.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}'];
        $replace = [$upTableName, $tableName];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . '.php', self::$allModelPath, $contents);
    }

    public static function generatorParamString(array $column)
    {

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToPHP($column);

        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            $default = MysqlOperation::getdefaultValue($v['DATA_TYPE']);
            $return = $return . '$where' . "['{$v['COLUMN_NAME']}']" . "= parameterCheck(" . '$request->input(' . "'{$v['COLUMN_NAME']}'" . '),' . "'{$v["DATA_TYPE"]}'" . ',' . "{$default}" . ');' . PHP_EOL;
        }
        return $return;
    }

    public static function generatorParamServiceString(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToPHP($column);
        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            $return = $return . 'isset($where' . "['{$v['COLUMN_NAME']}']" . ') && $' . "$lcTableName" . '->' . "{$v['COLUMN_NAME']}" . ' = ' . '$where' . "['{$v['COLUMN_NAME']}']" . ';' . PHP_EOL;
        }
        return $return;

    }

    public static function generatorAllRouter(bool $print = false)
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $k => $v) {
                self::generatorRouter($v['TABLE_NAME'], $print);
            }
        }
    }

    public static function generatorRouter(string $tableName, bool $print = false)
    {

        $camelizeTableName = Hump::camelize($tableName);
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(false, 'Router.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}', '{$camelizeTableName}'];
        $replace = [$upTableName, $tableName, $camelizeTableName];
        $content = str_replace($search, $replace, $content);

//        $contents = self::$fileHeaer . PHP_EOL . $content;
        //右键查看源代码
        if ($print) {
            print_r($content);
        } else {
            File::writeToFile('router.php', './', $content . PHP_EOL, 'a+', true);
        }
    }


    public static function generatorIfParamServiceString(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToPHP($column);
        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

//            $return = $return . 'isset($where' . "['{$v['COLUMN_NAME']}']" . ') && $' . "$lcTableName" . '->' . "{$v['COLUMN_NAME']}" . ' = ' . '$where' . "['{$v['COLUMN_NAME']}']" . ';' . PHP_EOL;

            $return = $return . 'if (!empty($where' . "['{$v['COLUMN_NAME']}']" . ')) {' . PHP_EOL .
                '$' . $lcTableName . '=' . '$' . $lcTableName . '->where(' . "'{$v['COLUMN_NAME']}'" . ', $where[' . "'{$v['COLUMN_NAME']}'" . ']);' . PHP_EOL . '}' . PHP_EOL;
        }
        return $return;
    }
}
