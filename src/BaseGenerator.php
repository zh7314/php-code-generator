<?php

namespace ZX;

abstract class BaseGenerator
{
    //模版文件路径
    protected static $templatePath = __DIR__ . DIRECTORY_SEPARATOR . 'Template';

    //获取模版路径
    public static function getTemplatePath()
    {
        return self::$templatePath;
    }

    //获取app path
    public abstract static function getAppPath();

    //获取控制器path
    public abstract static function getControllerPath();

    //获取服务path
    public abstract static function getServicePath();

    //获取模型path
    public abstract static function getModelPath();

    //设置控制全路径
    public abstract static function setAllControllerPath(string $allControllerPath);

    //设置服务全路径
    public abstract static function setAllServicePath(string $allServicePath);

    //设置模型全路径
    public abstract static function setAllModelPath(string $allModelPath);

    //获取当前文件路径
    public abstract static function getClassName();

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

}
