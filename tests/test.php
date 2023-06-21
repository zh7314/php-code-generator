<?php

include_once './../src/BaseGenerator.php';
include_once './../src/File.php';
include_once './../src/Hump.php';
include_once './Function.php';
include_once './../src/Mysql.php';
include_once './../src/MysqlOperation.php';
include_once './../src/Generator/LaravelCamel.php';
include_once './../src/Generator/LaravelSnake.php';
include_once './../src/Generator/LaravelSoftDelCamel.php';
include_once './../src/Generator/LaravelSoftDelSnake.php';

use ZX\MysqlOperation;
use ZX\File;
use ZX\Generator\LaravelCamel;
use ZX\Generator\LaravelSnake;
use ZX\Generator\LaravelSoftDelCamel;
use ZX\Generator\LaravelSoftDelSnake;

$param = [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'v2',
    'charset' => 'utf8',
    'user' => 'root',
    'pwd' => 'root'
];

MysqlOperation::setConnection($param);

/*
 * 非软删除版本 变量蛇形命名模版
 */

File::deldir('./' . LaravelSnake::getAppPath() . DIRECTORY_SEPARATOR);
LaravelSnake::generatorAllTable();
LaravelSnake::generatorAllRouter();


/*
 * 非软删除版本 变量驼峰命名模版
 */

//File::deldir('./' . LaravelCamel::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelCamel::generatorAllTable();
//LaravelCamel::generatorAllRouter();

/*
 * 软删除版本代码，变量蛇形命名模版
 */

//File::deldir('./' . LaravelSoftDelSnake::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelSoftDelSnake::generatorAllTable();
//LaravelSoftDelSnake::generatorAllRouter();

/*
 * 软删除版本代码，变量驼峰命名模版
 */

//File::deldir('./' . LaravelSoftDelCamel::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelSoftDelCamel::generatorAllTable();
//LaravelSoftDelCamel::generatorAllRouter();

/*
 * 单个表生产
 */

//LaravelSnake::generatorTable('admin');
//LaravelSnake::generatorTable('admin_group');

