<?php

namespace ZX;

use ZX\Hump;
use ZX\MysqlOperation;
use ZX\File;

abstract class BaseGenerator
{
    protected static $controllerPrefix = '';
    protected static $controllerSuffix = 'Controller';
    protected static $servicePrefix = '';
    protected static $serviceSuffix = 'Service';
    protected static $modelPrefix = '';
    protected static $modelSuffix = 'Model';
    protected static $controllerPath = 'Controllers' . DIRECTORY_SEPARATOR . 'Admin';
    protected static $servicePath = 'Services' . DIRECTORY_SEPARATOR . 'Admin';
    protected static $modelPath = 'Models';
    protected static $appPath = 'app';
    protected static $httpPath = 'Http';
    protected static $allControllerPath = 'Controllers';
    protected static $allServicePath = 'Services';
    protected static $allModelPath = 'Models';
    protected static $baseTemplatePath = __DIR__ . DIRECTORY_SEPARATOR . 'Template';

    //文件头部
    protected static $fileHeaer = '<?php ';

    //不需要处理的字段
    protected static $notDeal = ['id', 'create_at', 'update_at', 'is_delete', 'delete_at', 'create_time', 'update_time'];

    //生成全部表的代码
    public abstract static function generatorAllTable();

    //生成一张表的代码
    public abstract static function generatorTable(string $tableName);

    //生成Controller代码
    public abstract static function generatorController(string $tableName, array $column);

    //生成Service代码
    public abstract static function generatorService(string $tableName, array $column);

    //生成Model代码
    public abstract static function generatorModel(string $tableName, array $column);

    //生成Controller参数字符串代码
    public abstract static function generatorParamString(array $column);

    //生成Service参数字符串代码
    public abstract static function generatorParamServiceString(string $tableName, array $column);

    //生成Service参数 if代码
    public abstract static function generatorIfParamServiceString(string $tableName, array $column);


    public static function getControllerPrefix(): string
    {
        return self::$controllerPrefix;
    }

    public static function setControllerPrefix(string $controllerPrefix): void
    {
        self::$controllerPrefix = $controllerPrefix;
    }

    public static function getControllerSuffix(): string
    {
        return self::$controllerSuffix;
    }

    public static function setControllerSuffix(string $controllerSuffix): void
    {
        self::$controllerSuffix = $controllerSuffix;
    }

    public static function getServicePrefix(): string
    {
        return self::$servicePrefix;
    }

    public static function setServicePrefix(string $servicePrefix): void
    {
        self::$servicePrefix = $servicePrefix;
    }

    public static function getServiceSuffix(): string
    {
        return self::$serviceSuffix;
    }


    public static function setServiceSuffix(string $serviceSuffix): void
    {
        self::$serviceSuffix = $serviceSuffix;
    }

    public static function getModelPrefix(): string
    {
        return self::$modelPrefix;
    }

    public static function setModelPrefix(string $modelPrefix): void
    {
        self::$modelPrefix = $modelPrefix;
    }

    public static function getModelSuffix(): string
    {
        return self::$modelSuffix;
    }

    public static function setModelSuffix(string $modelSuffix): void
    {
        self::$modelSuffix = $modelSuffix;
    }

    public static function getControllerPath(): string
    {
        return self::$controllerPath;
    }

    public static function setControllerPath(string $controllerPath): void
    {
        self::$controllerPath = $controllerPath;
    }

    public static function getServicePath(): string
    {
        return self::$servicePath;
    }

    public static function setServicePath(string $servicePath): void
    {
        self::$servicePath = $servicePath;
    }

    public static function getModelPath(): string
    {
        return self::$modelPath;
    }

    public static function setModelPath(string $modelPath): void
    {
        self::$modelPath = $modelPath;
    }

    public static function getAppPath(): string
    {
        return self::$appPath;
    }

    public static function setAppPath(string $appPath): void
    {
        self::$appPath = $appPath;
    }

    public static function getHttpPath(): string
    {
        return self::$httpPath;
    }

    public static function setHttpPath(string $httpPath): void
    {
        self::$httpPath = $httpPath;
    }

    public static function getAllControllerPath(): string
    {
        return self::$allControllerPath;
    }

    public static function setAllControllerPath(string $allControllerPath): void
    {
        self::$allControllerPath = $allControllerPath;
    }

    public static function getAllServicePath(): string
    {
        return self::$allServicePath;
    }

    public static function setAllServicePath(string $allServicePath): void
    {
        self::$allServicePath = $allServicePath;
    }

    public static function getAllModelPath(): string
    {
        return self::$allModelPath;
    }

    public static function setAllModelPath(string $allModelPath): void
    {
        self::$allModelPath = $allModelPath;
    }

    public static function getBaseTemplatePath(): string
    {
        return self::$baseTemplatePath;
    }

    public static function setBaseTemplatePath(string $baseTemplatePath): void
    {
        self::$baseTemplatePath = $baseTemplatePath;
    }

    public static function getNotDeal(): array
    {
        return self::$notDeal;
    }

    public static function setNotDeal(array $notDeal): void
    {
        self::$notDeal = $notDeal;
    }

}
