<?php

namespace ZX\Generator;

use ZX\Generator;
use ZX\Tool\File;
use ZX\Tool\Hump;
use ZX\Tool\MysqlOperation;

class Goravel extends Generator
{
    //文件头部
    protected static $fileHeaer = '';
    //文件后缀
    protected static $fileSuffix = '.go';
    //应用根路径
    protected static $appPath = 'app';
    //控制器文件后缀
    protected static $controllerSuffix = '_controller';
    //服务文件后缀
    protected static $serviceSuffix = '_service';
    //模型文件后缀
    protected static $modelSuffix = '';
    //模型文件后缀
    protected static $requestSuffix = '_request';
    //控制器文件路径
    protected static $controllerPath = 'http' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'admin';
    //服务文件路径
    protected static $servicePath = 'services' . DIRECTORY_SEPARATOR . 'admin';
    //模型文件路径
    protected static $modelPath = 'models';
    //请求文件路径
    protected static $requestPath = 'requests';
    //控制器目录名称
    protected static $allControllerPath = '';
    //服务目录名称
    protected static $allServicePath = '';
    //模型目录名称
    protected static $allModelPath = '';
    //模型目录名称
    protected static $allRequestPath = '';

    //不需要处理的字段
    protected static $notDeal = ['id', 'create_at', 'update_at', 'is_delete', 'delete_at', 'create_time', 'update_time'];

    public static function getClassName()
    {
        return basename(__CLASS__);
    }

    public static function genPath(string $path = './')
    {
        self::$allControllerPath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$controllerPath;
        self::$allServicePath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$servicePath;
        self::$allModelPath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$modelPath;
        self::$allRequestPath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$requestPath;

        File::makeFile(self::$allControllerPath);
        File::makeFile(self::$allServicePath);
        File::makeFile(self::$allModelPath);
        File::makeFile(self::$allRequestPath);
    }

    public static function genAllTable(bool $case = false)
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $k => $v) {
                self::genTable($v['TABLE_NAME']);
            }
        }
    }

    public static function genTable(string $tableName, bool $case = false)
    {
        //获取表的字段
        $column = MysqlOperation::getTableColumn($tableName, true);
        //生成目录
        self::genPath();

        //生产控制器
        self::genController($tableName, $column);
        //生产服务
        self::genService($tableName, $column);
        //生产模型
        self::genModel($tableName, $column);
        //生产请求
        self::genRequest($tableName, $column);
    }

    public static function genController(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Controller.template', self::getClassName());

        $search = ['{upTableName}'];
        $replace = [$upTableName];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$controllerSuffix . self::$fileSuffix, self::$allControllerPath, $contents);
    }

    public static function genService(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));
        $lcTableName = lcfirst($upTableName);

        $content = File::getFileContent(new self(), 'Service.template', self::getClassName());

        $ifParamString = self::genServiceIfParam($tableName, $column);
        $paramString = self::genServiceParam($tableName, $column);

        $search = ['{upTableName}', '{paramString}', '{lcTableName}', '{ifParamString}'];
        $replace = [$upTableName, $paramString, $lcTableName, $ifParamString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$serviceSuffix . self::$fileSuffix, self::$allServicePath, $contents);
    }

    public static function genModel(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Model.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}'];
        $replace = [$upTableName, $tableName];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$modelSuffix . self::$fileSuffix, self::$allModelPath, $contents);
    }

    public static function genParamString(array $column, bool $camel = false)
    {

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);

        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }
            //蛇形转驼峰
            $camel && $v['COLUMN_NAME'] = Hump::camelize($v['COLUMN_NAME']);

            $default = MysqlOperation::getdefaultValueToGolang($v['DATA_TYPE']);
            $return = $return . '$where' . "['{$v['COLUMN_NAME']}']" . "= parameterCheck(" . '$request->input(' . "'{$v['COLUMN_NAME']}'" . '),' . "'{$v["DATA_TYPE"]}'" . ',' . "{$default}" . ');' . PHP_EOL;
        }
        return $return;
    }

    public static function genParamServiceString(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);
        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            $upColumnName = ucfirst(Hump::camelize($v['COLUMN_NAME']));

            $str = <<<EOF
admin.{$upColumnName} = request.{$upColumnName}
EOF;
            $return = $return . $str . PHP_EOL;
        }
        return $return;

    }


    public static function genServiceIfParam(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);
        foreach ($array as $k => $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            $upColumnName = ucfirst(Hump::camelize($v['COLUMN_NAME']));
            $str = <<<EOF
if !gconv.IsEmpty(request.{$upColumnName}) {
	orm.Where("{$v['COLUMN_NAME']}", request.{$upColumnName})
}
EOF;

            $return = $return . $str . PHP_EOL;
        }
        return $return;
    }

    public static function genAllRouter(bool $print = false)
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $k => $v) {
                self::genRouter($v['TABLE_NAME'], $print);
            }
        }
    }

    public static function genRouter(string $tableName, bool $print = false)
    {

        $camelizeTableName = Hump::camelize($tableName);
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Router.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}', '{$camelizeTableName}'];
        $replace = [$upTableName, $tableName, $camelizeTableName];
        $content = str_replace($search, $replace, $content);

//        $content = self::$fileHeaer . PHP_EOL . $content;
        //右键查看源代码
        if ($print) {
            print_r($content);
        } else {
            File::writeToFile('router' . self::$fileSuffix, self::$allModelPath, $content . PHP_EOL, 'a+', true);
        }
    }

    public static function genRequest(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Model.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}'];
        $replace = [$upTableName, $tableName];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$requestSuffix . self::$fileSuffix, self::$allModelPath, $contents);
    }
}
