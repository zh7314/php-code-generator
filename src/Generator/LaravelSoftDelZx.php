<?php

namespace ZX\Generator;

use ZX\Generator;
use ZX\Tool\File;
use ZX\Tool\Hump;
use ZX\Tool\MysqlOperation;

class LaravelSoftDelZx extends Generator
{
    //文件头部
    protected static $fileHeaer = '<?php ';
    //文件后缀
    protected static $fileSuffix = '.php';
    //控制器文件后缀
    protected static $controllerSuffix = 'Controller';
    //服务文件后缀
    protected static $serviceSuffix = 'Service';
    //模型文件后缀
    protected static $modelSuffix = '';
    //控制器文件路径
    protected static $controllerPath = 'Controllers' . DIRECTORY_SEPARATOR . 'Admin';
    //服务文件路径
    protected static $servicePath = 'Services' . DIRECTORY_SEPARATOR . 'Admin';
    //模型文件路径
    protected static $modelPath = 'Models';
    //应用根路径
    protected static $appPath = 'app' . DIRECTORY_SEPARATOR . 'Http';
    //控制器目录名称
    protected static $allControllerPath = '';
    //服务目录名称
    protected static $allServicePath = '';
    //模型目录名称
    protected static $allModelPath = '';
    //不需要处理的字段
    protected static $notDeal = ['id', 'create_at', 'update_at', 'is_delete', 'delete_at', 'create_time', 'update_time'];

    public static function getClassName()
    {
        return basename(__CLASS__);
    }

    public static function genAllTable()
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $k => $v) {
                self::genTable($v['TABLE_NAME']);
            }
        }
    }

    public static function genTable(string $tableName)
    {
        //获取表的字段
        $column = MysqlOperation::getTableColumn($tableName);
        //生成目录
        self::genPath();
        //生产控制器
        self::genController($tableName, $column);
        //生产服务
        self::genService($tableName, $column);
        //生产模型
        self::genModel($tableName, $column);
    }

    public static function genPath(string $path = './')
    {
        self::$allControllerPath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$controllerPath;
        self::$allServicePath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$servicePath;
        self::$allModelPath = $path . self::$appPath . DIRECTORY_SEPARATOR . self::$modelPath;

        File::makeFile(self::$allControllerPath);
        File::makeFile(self::$allServicePath);
        File::makeFile(self::$allModelPath);
    }

    public static function genController(string $tableName, array $column)
    {
        //表名
        $upTableName = ucfirst(Hump::camelize($tableName));
        //列数据
        $paramString = self::genControllerParam($column);

        $content = File::getFileContent(new self(), 'Controller.template', self::getClassName());

        $search = ['{upTableName}', '{paramString}'];
        $replace = [$upTableName, $paramString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$controllerSuffix . self::$fileSuffix, self::$allControllerPath, $contents);
    }

    public static function genControllerParam(array $column, bool $camel = false)
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
            $str = <<<EOF
            \$where['{$v['COLUMN_NAME']}'] = parameterCheck(\$request->input('{$v['COLUMN_NAME']}'), '{$v["DATA_TYPE"]}', {$default});
EOF;
            $return = $return . $str . PHP_EOL;
        }
        return $return;
    }

    public static function genService(string $tableName, array $column)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));
        $lcTableName = lcfirst($upTableName);

        $content = File::getFileContent(new self(), 'Service.template', self::getClassName());

        $paramString = self::generatorParamServiceString($tableName, $column);
        $ifParamString = self::generatorIfParamServiceString($tableName, $column);

        $search = ['{upTableName}', '{paramString}', '{lcTableName}', '{ifParamString}'];
        $replace = [$upTableName, $paramString, $lcTableName, $ifParamString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile($upTableName . self::$serviceSuffix . self::$fileSuffix, self::$allServicePath, $contents);
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

            $str = <<<EOF
            if (!empty(\$where['{$v['COLUMN_NAME']}'])) {
            \${$lcTableName} = \${$lcTableName}->where('{$v['COLUMN_NAME']}', \$where['{$v['COLUMN_NAME']}']);
            }
EOF;
            $return = $return . $str . PHP_EOL;
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

            $str = <<<EOF
            isset(\$where['{$v['COLUMN_NAME']}']) && \${$lcTableName}->{$v['COLUMN_NAME']} = \$where['{$v['COLUMN_NAME']}'];
EOF;
            $return = $return . $str . PHP_EOL;
        }
        return $return;

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


    public static function genAllRouter()
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $k => $v) {
                self::genRouter($v['TABLE_NAME']);
            }
        }
    }

    public static function genRouter(string $tableName)
    {

        $camelizeTableName = Hump::camelize($tableName);
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Router.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}', '{$camelizeTableName}'];
        $replace = [$upTableName, $tableName, $camelizeTableName];
        $content = str_replace($search, $replace, $content);

//        $content = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile('router' . self::$fileSuffix, './', $content . PHP_EOL, 'a+', true);
    }
}
