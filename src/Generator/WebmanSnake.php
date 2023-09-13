<?php

namespace ZX\Generator;

use ZX\BaseGenerator;
use ZX\Tool\File;
use ZX\Tool\Hump;
use ZX\Tool\MysqlOperation;

class WebmanSnake extends BaseGenerator
{
    //文件后缀
    protected static $fileSuffix = '.php';
    //控制器文件后缀
    protected static $controllerSuffix = 'Controller';
    //服务文件后缀
    protected static $serviceSuffix = 'Service';
    //模型文件后缀
    protected static $modelSuffix = '';
    //控制器文件路径
    protected static $controllerPath = 'controller' . DIRECTORY_SEPARATOR . 'Admin';
    //服务文件路径
    protected static $servicePath = 'service' . DIRECTORY_SEPARATOR . 'Admin';
    //模型文件路径
    protected static $modelPath = 'model';
    //应用根路径
    protected static $appPath = 'app';
    //控制器目录名称
    protected static $allControllerPath = '';
    //服务目录名称
    protected static $allServicePath = '';
    //模型目录名称
    protected static $allModelPath = '';
    //文件头部
    protected static $fileHeaer = '<?php ';
    //不需要处理的字段
    protected static $notDeal = ['id', 'create_at', 'update_at', 'is_delete', 'delete_at', 'create_time', 'update_time'];

    //获取app path
    public static function getAppPath()
    {
        return self::$appPath;
    }

    public static function getControllerPath()
    {
        return self::$controllerPath;
    }

    public static function getServicePath()
    {
        return self::$servicePath;
    }

    public static function getModelPath()
    {
        return self::$modelPath;
    }

    public static function getClassName()
    {
        return basename(__CLASS__);
    }

    public static function setAllControllerPath(string $allControllerPath)
    {
        self::$allControllerPath = $allControllerPath;
    }

    public static function setAllServicePath(string $allServicePath)
    {
        self::$allServicePath = $allServicePath;
    }

    public static function setAllModelPath(string $allModelPath)
    {
        self::$allModelPath = $allModelPath;
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
        //获取表的字段
        $column = MysqlOperation::getTableColumn($tableName);
        //生成目录
        File::generatorPath(new self());
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

        $content = File::getFileContent(new self(), 'Controller.template', self::getClassName());

        $search = ['{upTableName}', '{paramString}'];
        $replace = [$upTableName, $paramString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$controllerSuffix . self::$fileSuffix, self::$allControllerPath, $contents);
    }

    public static function generatorService(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));
        $lcTableName = lcfirst($upTableName);

        $paramString = self::generatorParamServiceString($tableName, $column);

        $content = File::getFileContent(new self(), 'Service.template', self::getClassName());

        $ifParamString = self::generatorIfParamServiceString($tableName, $column);

        $search = ['{upTableName}', '{paramString}', '{lcTableName}', '{ifParamString}'];
        $replace = [$upTableName, $paramString, $lcTableName, $ifParamString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$serviceSuffix . self::$fileSuffix, self::$allServicePath, $contents);
    }

    public static function generatorModel(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Model.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}'];
        $replace = [$upTableName, $tableName];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$modelSuffix . self::$fileSuffix, self::$allModelPath, $contents);
    }

    public static function generatorParamString(array $column, bool $camel = false)
    {

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToPHP($column);

        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }
            //蛇形转驼峰
            $camel && $v['COLUMN_NAME'] = Hump::camelize($v['COLUMN_NAME']);

            $default = MysqlOperation::getdefaultValue($v['DATA_TYPE']);
            $return = $return . '$where' . "['{$v['COLUMN_NAME']}']" . "= parameterCheck(" . '$request->input(' . "'{$v['COLUMN_NAME']}'" . '),' . "'{$v["DATA_TYPE"]}'" . ',' . "{$default}" . ');' . PHP_EOL;
        }
        return $return;
    }

    public static function generatorParamServiceString(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToPHP($column);
        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }
            //蛇形转驼峰
            $camel && $v['COLUMN_NAME'] = Hump::camelize($v['COLUMN_NAME']);

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

        $content = File::getFileContent(new self(), 'Router.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}', '{$camelizeTableName}'];
        $replace = [$upTableName, $tableName, $camelizeTableName];
        $content = str_replace($search, $replace, $content);

        $content = self::$fileHeaer . PHP_EOL . $content;
        //右键查看源代码
        if ($print) {
            print_r($content);
        } else {
            File::writeToFile('router.php', './', $content . PHP_EOL, 'a+', true);
        }
    }


    public static function generatorIfParamServiceString(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToPHP($column);
        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            //蛇形转驼峰
            $camel && $v['COLUMN_NAME'] = Hump::camelize($v['COLUMN_NAME']);

            $return = $return . 'if (!empty($where' . "['{$v['COLUMN_NAME']}']" . ')) {' . PHP_EOL .
                '$' . $lcTableName . '=' . '$' . $lcTableName . '->where(' . "'{$v['COLUMN_NAME']}'" . ', $where[' . "'{$v['COLUMN_NAME']}'" . ']);' . PHP_EOL . '}' . PHP_EOL;
        }
        return $return;
    }


}
