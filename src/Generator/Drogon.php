<?php

namespace ZX\Generator;

use ZX\Generator;
use ZX\Tool\File;
use ZX\Tool\Hump;
use ZX\Tool\MysqlOperation;

class Drogon extends Generator
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
    protected static $requestPath = 'requests' . DIRECTORY_SEPARATOR . 'admin';
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

    public static function genAllTable(bool $camel = false)
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $v) {
                self::genTable($v['TABLE_NAME'], $camel);
            }
        }
    }

    public static function genTable(string $tableName, bool $camel = false)
    {
        //获取表的字段
        $column = MysqlOperation::getTableColumn($tableName, true);
        //生成目录
        self::genPath();

        //生产控制器
        self::genController($tableName, $column, $camel);
        //生产服务
        self::genService($tableName, $column, $camel);
        //生产模型
        self::genModel($tableName, $column, $camel);
        //生产请求
        self::genRequest($tableName, $column, $camel);
    }

    public static function genController(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Controller.template', self::getClassName());

        $search = ['{upTableName}'];
        $replace = [$upTableName];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . $content;

        File::writeToFile($tableName . self::$controllerSuffix . self::$fileSuffix, self::$allControllerPath, $contents);
    }

    public static function genService(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));
        $lcTableName = lcfirst($upTableName);

        $content = File::getFileContent(new self(), 'Service.template', self::getClassName());

        $ifParamString = self::genServiceIfParam($tableName, $column, $camel);
        $paramString = self::genServiceParam($tableName, $column, $camel);

        $search = ['{upTableName}', '{paramString}', '{lcTableName}', '{ifParamString}'];
        $replace = [$upTableName, $paramString, $lcTableName, $ifParamString];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . $content;

        File::writeToFile($tableName . self::$serviceSuffix . self::$fileSuffix, self::$allServicePath, $contents);
    }

    public static function genServiceIfParam(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);
        foreach ($array as $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            $upColumnName = ucfirst(Hump::camelize($v['COLUMN_NAME']));

            $str ='';
            if ($v['DATA_TYPE'] == 'time.Time') {
                $str = <<<EOF
if !request.{$upColumnName}.IsZero() {
	orm = orm.Where("{$v['COLUMN_NAME']}", request.{$upColumnName})
}
EOF;
            }else{
                $str = <<<EOF
if !gconv.IsEmpty(request.{$upColumnName}) {
	orm = orm.Where("{$v['COLUMN_NAME']}", request.{$upColumnName})
}
EOF;
            }

            $return = $return . $str . PHP_EOL;
        }
        return $return;
    }

    public static function genServiceParam(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);
        foreach ($array as $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }

            $upColumnName = ucfirst(Hump::camelize($v['COLUMN_NAME']));

            $str = '';
            if ($v['DATA_TYPE'] == 'string') {
                $str = <<<EOF
	if !gconv.IsEmpty(request.{$upColumnName}) {
		{$lcTableName}.{$upColumnName} = html.EscapeString(request.{$upColumnName})
	}
EOF;
            } elseif ($v['DATA_TYPE'] == 'time.Time') {
                $str = <<<EOF
	if !request.{$upColumnName}.IsZero() {
		{$lcTableName}.{$upColumnName} = request.{$upColumnName}
	}
EOF;
            } else {
                $str = <<<EOF
	if !gconv.IsEmpty(request.{$upColumnName}) {
		{$lcTableName}.{$upColumnName} = request.{$upColumnName}
	}
EOF;
            }

            $return = $return . $str . PHP_EOL;
        }
        return $return;
    }

    public static function genModel(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Model.template', self::getClassName());

        $hasImport = [];
        $paramString = self::genModelParam($tableName, $column, $camel, $hasImport);

        $import = '';
        if ($hasImport['time'] > 0) {
            $import = <<<EOF
import (
	"github.com/goravel/framework/support/carbon"
	"time"
)
EOF;
        } else {
            $import = <<<EOF
import "github.com/goravel/framework/support/carbon"
EOF;
        }

        $search = ['{upTableName}', '{paramString}', '{tableName}', '{import}'];
        $replace = [$upTableName, $paramString, $tableName, $import];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . $content;

        File::writeToFile($tableName . self::$modelSuffix . self::$fileSuffix, self::$allModelPath, $contents);
    }

    public static function genModelParam(string $tableName, array $column, bool $camel = false, array &$hasImport = [])
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $lcTableName = lcfirst($upTableName);

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);

        $hasImport['time'] = 0;
        foreach ($array as $v) {
            $upColumnName = ucfirst(Hump::camelize($v['COLUMN_NAME']));

            if ($v['COLUMN_NAME'] == 'id') {
                continue;
            }

            $str = '';
            if ($v['COLUMN_NAME'] == 'create_at' || $v['COLUMN_NAME'] == 'update_at') {
                $str = <<<EOF
    {$upColumnName}            carbon.DateTime           `gorm:"column:{$v['COLUMN_NAME']};->" json:"{$v['COLUMN_NAME']}"`           // comment {$v['COLUMN_COMMENT']}
EOF;
            } else {
                if ($v['DATA_TYPE'] == 'time.Time') {
                    $hasImport['time']++;
                }
                $str = <<<EOF
    {$upColumnName}            {$v['DATA_TYPE']}           `gorm:"column:{$v['COLUMN_NAME']}" json:"{$v['COLUMN_NAME']}"`           // comment {$v['COLUMN_COMMENT']}
EOF;
            }

            $return = $return . $str . PHP_EOL;
        }
        return $return;
    }

    public static function genAllRouter()
    {
        $table = MysqlOperation::getAllTableName();

        if (!empty($table)) {
            foreach ($table as $v) {
                self::genRouter($v['TABLE_NAME']);
            }
        }
    }

    public static function genRouter(string $tableName)
    {
        $camelizeTableName = Hump::camelize($tableName);
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Router.template', self::getClassName());

        $search = ['{upTableName}', '{tableName}', '{camelizeTableName}'];
        $replace = [$upTableName, $tableName, $camelizeTableName];
        $content = str_replace($search, $replace, $content);

//        $content = self::$fileHeaer . PHP_EOL . $content;

        File::writeToFile('router' . self::$fileSuffix, './', $content . PHP_EOL, 'a+', true);
    }

    public static function genRequest(string $tableName, array $column, bool $camel = false)
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $content = File::getFileContent(new self(), 'Request.template', self::getClassName());

        $hasImport = [];
        $paramString = self::genRequestParam($tableName, $column, $camel, $hasImport);

        $import = '';
        if ($hasImport['time'] > 0) {
            $import = <<<EOF
import "time"
EOF;
        }
        $search = ['{upTableName}', '{paramString}', '{import}'];
        $replace = [$upTableName, $paramString, $import];
        $content = str_replace($search, $replace, $content);

        $contents = self::$fileHeaer . $content;

        File::writeToFile($tableName . self::$requestSuffix . self::$fileSuffix, self::$allRequestPath, $contents);
    }

    public static function genRequestParam(string $tableName, array $column, bool $camel = false, array &$hasImport = [])
    {
        $upTableName = ucfirst(Hump::camelize($tableName));

        $return = '';
        $array = MysqlOperation::transforColumnMysqlToGolang($column);

        $hasImport['time'] = 0;
        foreach ($array as $v) {
            if (in_array($v['COLUMN_NAME'], self::$notDeal)) {
                continue;
            }
            if ($v['DATA_TYPE'] == 'time.Time') {
                $hasImport['time']++;
            }

            $upColumnName = ucfirst(Hump::camelize($v['COLUMN_NAME']));

            if ($camel) {
                $v['COLUMN_NAME'] = Hump::camelize($v['COLUMN_NAME']);
            }

//            $str = '';
//            if ($v['DATA_TYPE'] == 'string') {
//                $str = <<<EOF
//    {$upColumnName}            {$v['DATA_TYPE']}           `form:"{$v['COLUMN_NAME']}" json:"{$v['COLUMN_NAME']}"`           // comment {$v['COLUMN_COMMENT']}
//EOF;
//            } else {
            $str = <<<EOF
    {$upColumnName}            {$v['DATA_TYPE']}           `form:"{$v['COLUMN_NAME']}" json:"{$v['COLUMN_NAME']}"`           // comment {$v['COLUMN_COMMENT']}
EOF;
//            }

            $return = $return . $str . PHP_EOL;
        }
        return $return;
    }
}
