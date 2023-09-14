<?php

include_once './Function.php';
include_once './../src/Tool/File.php';
include_once './../src/Tool/Hump.php';
include_once './../src/Tool/Mysql.php';
include_once './../src/Tool/MysqlOperation.php';
include_once './../src/BaseGenerator.php';
include_once './../src/Generator/LaravelCamel.php';
include_once './../src/Generator/LaravelSnake.php';
include_once './../src/Generator/LaravelSoftDelZx.php';
include_once './../src/Generator/WebmanCamel.php';
include_once './../src/Generator/WebmanSnake.php';
include_once './../src/Generator/GoravelSnake.php';

use ZX\Tool\File;
use ZX\Tool\MysqlOperation;
use ZX\Generator\LaravelCamel;
use ZX\Generator\LaravelSnake;
use ZX\Generator\LaravelSoftDelZx;
use ZX\Generator\WebmanCamel;
use ZX\Generator\WebmanSnake;
use ZX\Generator\GoravelSnake;

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

//File::deldir('./' . LaravelSnake::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelSnake::generatorAllTable();
//LaravelSnake::generatorAllRouter();


/*
 * 非软删除版本 变量驼峰命名模版
 */

//File::deldir('./' . LaravelCamel::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelCamel::generatorAllTable();
//LaravelCamel::generatorAllRouter();

/*
 * 软删除版本特殊代码，变量蛇形命名模版
 */

//File::deldir('./' . LaravelSoftDelZx::getAppPath() . DIRECTORY_SEPARATOR);
//LaravelSoftDelZx::generatorAllTable();
//LaravelSoftDelZx::generatorAllRouter();

/*
 * 非软删除版本 变量驼峰命名模版
 */

//File::deldir('./' . WebmanCamel::getAppPath() . DIRECTORY_SEPARATOR);
//WebmanCamel::generatorAllTable();
//WebmanCamel::generatorAllRouter();

/*
 * 非软删除版本 变量蛇形命名模版
 */

//File::deldir('./' . WebmanSnake::getAppPath() . DIRECTORY_SEPARATOR);
//WebmanSnake::generatorAllTable();
//WebmanSnake::generatorAllRouter();

/*
 * 非软删除版本 go 变量蛇形命名模版
 */

File::deldir('./' . GoravelSnake::getAppPath() . DIRECTORY_SEPARATOR);
GoravelSnake::generatorAllTable();
GoravelSnake::generatorAllRouter();
