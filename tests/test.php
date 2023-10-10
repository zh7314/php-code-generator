<?php

include_once './Function.php';
include_once './../src/Tool/File.php';
include_once './../src/Tool/Hump.php';
include_once './../src/Tool/Mysql.php';
include_once './../src/Tool/MysqlOperation.php';
include_once './../src/Generator.php';
include_once './../src/Generator/Laravel.php';
include_once './../src/Generator/LaravelSoftDelZx.php';
include_once './../src/Generator/Webman.php';
include_once './../src/Generator/Goravel.php';
include_once './../src/Generator/Drogon.php';

use ZX\Tool\MysqlOperation;
use ZX\Generator\Laravel;
use ZX\Generator\LaravelSoftDelZx;
use ZX\Generator\Webman;
use ZX\Generator\Goravel;
use ZX\Generator\Drogon;

$param = [
   'type' => 'mysql',
   'host' => '127.0.0.1',
   'port' => '3306',
   'dbname' => 'web',
   'charset' => 'utf8',
   'user' => 'root',
   'pwd' => 'root'
];

MysqlOperation::setConnection($param);

/*
 * laravel生成器,参考项目: https://github.com/zh7314/zx-laravel-website   https://gitee.com/open-php/zx-laravel-website
 * $camel true => case camel,true => case snake
 */

// Laravel::genAllTable();
// Laravel::genAllRouter();

// Laravel::genTable("table_name");
// Laravel::genRouter("table_name");

/*
 * webman 生成器,配合使用项目: https://github.com/zh7314/zx-webman-website   https://gitee.com/open-php/zx-webman-website
 * $camel true => case camel,true => case snake
 */

// Webman::genAllTable();
// Webman::genAllRouter();

// Webman::genTable("table_name");
// Webman::genRouter("table_name");

/*
 * goravel 生成器,配合使用项目: https://github.com/zh7314/zx-goravel-website   https://gitee.com/open-php/zx-goravel-website
 * $camel true => case camel,true => case snake
 */

// Goravel::genAllTable();
// Goravel::genAllRouter();

// Goravel::genTable("table_name");
// Goravel::genRouter("table_name");

/*
 * drogon 生成器,配合使用项目: https://github.com/zh7314/zx-drogon-website   https://gitee.com/open-php/zx-drogon-website
 * $camel true => case camel,true => case snake
 */

// Drogon::genAllTable();
// Drogon::genAllRouter();

// Drogon::genTable("table_name");
// Drogon::genRouter("table_name");

/*
 * laravelzx 定制化生成器
 */

// LaravelSoftDelZx::genAllTable();
// LaravelSoftDelZx::genAllRouter();

// LaravelSoftDelZx::genTable("table_name");
// LaravelSoftDelZx::genRouter("table_name");
