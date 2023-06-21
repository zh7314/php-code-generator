<?php

include_once './../src/BaseGenerator.php';
include_once './../src/File.php';
include_once './../src/Hump.php';
include_once './Function.php';
include_once './../src/Mysql.php';
include_once './../src/MysqlOperation.php';
include_once './../src/Generator/LaravelZx.php';
include_once './../src/Generator/LaravelStd.php';

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
     'dbname' => 'qyy_v2',
     'charset' => 'utf8',
     'user' => 'root',
     'pwd' => 'root'
 ];

MysqlOperation::setConnection($param);

//非软删除版本 变量蛇形命名,
File::deldir('./' . LaravelSnake::getAppPath() . DIRECTORY_SEPARATOR);
LaravelSnake::generatorAllTable();
LaravelSnake::generatorAllRouter();

// 软删除版本代码
//File::deldir('./' . LaravelZx::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelZx::generatorAllTable();
//LaravelZx::generatorAllRouter();

//单个表生产
//LaravelGenerator::generatorTable('admin');
//LaravelGenerator::generatorTable('admin_group');

