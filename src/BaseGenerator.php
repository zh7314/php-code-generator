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

}
