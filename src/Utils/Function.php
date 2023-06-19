<?php

if (!function_exists('parameterCheck')) {

    /**
     * 如果是double的话，也使用float，需要处理其他类型数据自己添加
     * @param type $param 需要处理的参数
     * @param type $ExpectDataType 期望返回数据类型
     * @param type $defaultValue 如果没有值返回的默认值
     * @return type
     * @throws Exception
     */
    function parameterCheck(mixed $param, string $ExpectDataType, mixed $defaultValue)
    {
        $dataType = ['int', 'float', 'string', 'array', 'bool'];
        if (!in_array($ExpectDataType, $dataType)) {
            throw new Exception('数据类型不存在');
        }
        if (empty($param)) {
            return $defaultValue;
        }
        if ($ExpectDataType == 'int') {
            return (int)htmlFilter($param);
        } elseif ($ExpectDataType == 'float') {
            return (float)htmlFilter($param);
        } elseif ($ExpectDataType == 'string') {
            return (string)htmlFilter($param);
        } elseif ($ExpectDataType == 'array') {
            return (array)$param;
        } elseif ($ExpectDataType == 'bool') {
            return (bool)htmlFilter($param);
        }
    }

}

if (!function_exists('htmlFilter')) {

    /**
     * @param type $param 需要html转义和去除空格的参数
     * @throws Exception
     */
    function htmlFilter($param)
    {
        return htmlspecialchars(trim($param), ENT_QUOTES, "UTF-8");
    }
}

if (!function_exists('isMobile')) {

    function isMobile($mobile)
    {
        return preg_match("/^1(3\d|4[5-9]|5[0-35-9]|6[2567]|7[0-8]|8\d|9[0-35-9])\d{8}$/", $mobile);

    }
}

