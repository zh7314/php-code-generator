# php-code-generator

[English doc](./README.en.md)

### 介绍
基于php的代码生成器，根据数据库表快速生成CURD方法，是基于渲染模板的方法

### 运行环境
php `^7.2|^8.0`

### 支持范围
目前仅支持laravel，webman，后续会支持goravel，drogon等，也欢迎提交模板
- [x] PHP-laravel 蛇形命名
- [x] PHP-laravel 驼峰命名
- [x] PHP-webman 蛇形命名
- [x] PHP-webman 驼峰命名
- [ ] Golang-goravel 蛇形命名
- [ ] Golang-goravel 驼峰命名
- [ ] CPP-drogon 蛇形命名
- [ ] CPP-drogon 驼峰命名

### 支持 composer
```
composer require zx/php-code-generator
```

### 关于软删除版本
1，不在单独提供laravel，webman等使用`Eloquent ORM`的框架，因只需要单独在`model`里面添加`use SoftDeletes`即可
2，驼峰命令在orm里面的转换，`composer require kirkbushell/eloquence`
```
添加 eloquence service provider 在你的 config/app.php 文件中

'providers' => [

        /*
         * Application Service Providers...
         */
        Eloquence\EloquenceServiceProvider::class,
    ],
```
实例：
```
<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    // 蛇形命名转驼峰
    use CamelCasing;

    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'feedback';
    // 主键id
    protected $primaryKey = 'id';
    // 不可被批量赋值的字段
    protected $guarded = [];
    // 不维护时间字段
    public $timestamps = false;
    // 返回隐藏的字段
    protected $hidden = ['delete_at'];
    // 返回显示的字段
    protected $visible = [];
    // 自定义软删除字段 默认 deleted_at
    const DELETED_AT = 'delete_at';
}

```
`Eloquent ORM`软删除使用文档：https://www.cnblogs.com/zx-admin/p/17497555.html  
`Eloquent ORM`驼峰命名转换使用文档：https://www.cnblogs.com/zx-admin/p/17493699.html   


### 使用方法
1, 下载该工具包代码，保障当前环境有php的运行环境 
2, 查看`tests/test.php`的代码有测试用例   
3, 在 `tests/Tools`有针对laravel和webman的代码工具包   
4, 如果不理解怎么使用可以参看 webman项目`https://github.com/zh7314/zx-webman-website` OR laravel项目`https://github.com/zh7314/zx-website`   
5, 每次生成前建议删除`tests`的目录下，然后吧生成好的代码复制到对应的项目文件下，直接生成在项目目录
容易造成代码覆盖，所以推荐自己复制进去   


#### 使用例子
```
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

use ZX\Generator\WebmanCamel;
use ZX\Generator\WebmanSnake;
use ZX\Tool\File;
use ZX\Tool\MysqlOperation;
use ZX\Generator\LaravelCamel;
use ZX\Generator\LaravelSnake;
use ZX\Generator\LaravelSoftDelZx;

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

File::deldir('./' . LaravelSnake::getAppPath() . DIRECTORY_SEPARATOR);
LaravelSnake::generatorAllTable();
LaravelSnake::generatorAllRouter();


/*
 * 非软删除版本 变量驼峰命名模版
 */

File::deldir('./' . LaravelCamel::getAppPath() . DIRECTORY_SEPARATOR);
LaravelCamel::generatorAllTable();
LaravelCamel::generatorAllRouter();

/*
 * 软删除版本特殊代码，变量蛇形命名模版
 */

File::deldir('./' . LaravelSoftDelZx::getAppPath() . DIRECTORY_SEPARATOR);
LaravelSoftDelZx::generatorAllTable();
LaravelSoftDelZx::generatorAllRouter();

/*
 * 非软删除版本 变量驼峰命名模版
 */

File::deldir('./' . WebmanCamel::getAppPath() . DIRECTORY_SEPARATOR);
WebmanCamel::generatorAllTable();
WebmanCamel::generatorAllRouter();

/*
 * 非软删除版本 变量蛇形命名模版
 */

File::deldir('./' . WebmanSnake::getAppPath() . DIRECTORY_SEPARATOR);
WebmanSnake::generatorAllTable();
WebmanSnake::generatorAllRouter();
```

6, 如果你直接使用默认模板你可能需要一些辅助代码   
他们会在`tests/Tools`的`laravel/utils`里面
在`composer.json`里面加上   
```
"autoload": {
        "files": [
          "app/Utils/Function.php"
        ]
    }
```
7, 如果你不喜欢使用一些辅助代码，你可以把返回的代码改成
```
return response()->json(['code' => 200, 'msg' => '成功']);
```
这样标准laravel写法 ,代码检查也可以改成
```
$where['weixin_phone'] = !empty($request->weixin_phone) ? (string)htmlspecialchars(trim($request->weixin_phone), ENT_QUOTES, "UTF-8") : '';
```
#### 自定义模板
1,继承 `ZX\BaseGenerator`  
2,实现抽象方法，参照`XXGenerator` 的方法去实现自己的模板，通用的方法都有提供

#### 问题反馈
QQ群：247823727  
博客：https://www.cnblogs.com/zx-admin/   
gitee：https://gitee.com/open-php/php-code-generator   
github：https://github.com/zh7314
