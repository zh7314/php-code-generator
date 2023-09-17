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

use ZX\Tool\MysqlOperation;
use ZX\Generator\Laravel;
use ZX\Generator\LaravelSnake;
use ZX\Generator\LaravelSoftDelZx;
use ZX\Generator\Webman;
use ZX\Generator\Goravel;

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
 * 非软删除版本 变量蛇形命名模版
 */

//LaravelSnake::generatorAllTable();
//LaravelSnake::generatorAllRouter();


/*
 * 非软删除版本 变量驼峰命名模版
 */

//LaravelCamel::generatorAllTable();
//LaravelCamel::generatorAllRouter();

/*
 * 软删除版本特殊代码，变量蛇形命名模版
 */

//LaravelSoftDelZx::generatorAllTable();
//LaravelSoftDelZx::generatorAllRouter();

/*
 * 非软删除版本 变量驼峰命名模版
 */

//WebmanCamel::generatorAllTable();
//WebmanCamel::generatorAllRouter();

/*
 * 非软删除版本 变量蛇形命名模版
 */

//WebmanSnake::generatorAllTable();
//WebmanSnake::generatorAllRouter();

/*
 * 非软删除版本 go 变量蛇形命名模版
 */

//GoravelSnake::generatorAllTable();

Goravel::genTable("admin_group");
//Goravel::genRouter("admin_group");
