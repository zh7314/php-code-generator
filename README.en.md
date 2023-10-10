# code-generator

[中文文档](./README.md)

### introduce
A php based code generator that quickly generates CURD methods from database tables is a method based on rendering templates

### environment
php `^7.2|^8.0`

### support
Currently only support laravel, webman, goravel, drogon and so on will be supported in the future, also welcome to submit templates
- [x] PHP-laravel snake case
- [x] PHP-laravel camel case
- [x] PHP-webman snake case
- [x] PHP-webman camel case
- [x] Golang-goravel snake case
- [x] Golang-goravel camel case
- [ ] CPP-drogon snake case
- [ ] CPP-drogon camel case

### composer
```
composer require zx/php-code-generator
```

### About soft delete version
1，laravel, webman, etc. use Eloquent ORM frameworks, because you only need to add `use SoftDeletes` to `model` separately    
2，The hump commands the conversion inside the orm，`composer require kirkbushell/eloquence`
```
Add the eloquence service provider to your config/app.php file

'providers' => [

        /*
         * Application Service Providers...
         */
        Eloquence\EloquenceServiceProvider::class,
    ],
```
example：
```
<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    // Serpentine named turn hump
    use CamelCasing;

    // Soft delete
    use SoftDeletes;

    // table name
    protected $table = 'feedback';
    // primary key id
    protected $primaryKey = 'id';
    // A field that cannot be assigned in batches
    protected $guarded = [];
    // No time field is maintained
    public $timestamps = false;
    // Returns a hidden field
    protected $hidden = ['delete_at'];
    // 返回显示的字段
    protected $visible = [];
    // The default soft delete field is deleted_at
    const DELETED_AT = 'delete_at';
}

```
`Eloquent ORM`Soft delete usage document：https://www.cnblogs.com/zx-admin/p/17497555.html  
`Eloquent ORM`Hump naming conversion documentation：https://www.cnblogs.com/zx-admin/p/17493699.html


### how to use
1, Download the toolkit code to ensure that the current environment has a php running environment
2, Look at the `tests/test.php` code for test cases 
3, There are code kits for laravel and webman in `tests/Tools`
4, If you do not understand how to use can see, webman project `https://github.com/zh7314/zx-webman-website` OR laravel project`https://github.com/zh7314/zx-website`   
5, Before each generation, it is recommended to delete the directory of `tests`, and then copy the generated code to the corresponding project file. Directly generated 
in the project directory is easy to cause code coverage, so it is recommended to copy it yourself


#### use example
```
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
 * laravel生成器,参考项目: https://github.com/zh7314/zx-laravel-website   https://gitee.com/open-php/zx-laravel-website
 * $camel true => case camel,true => case snake
 */

Laravel::genAllTable();
Laravel::genAllRouter();

Laravel::genTable("table_name");
Laravel::genRouter("table_name");

/*
 * webman 生成器,配合使用项目: https://github.com/zh7314/zx-webman-website   https://gitee.com/open-php/zx-webman-website
 * $camel true => case camel,true => case snake
 */

Webman::genAllTable();
Webman::genAllRouter();

Webman::genTable("table_name");
Webman::genRouter("table_name");

/*
 * goravel 生成器,配合使用项目: https://github.com/zh7314/zx-goravel-website   https://gitee.com/open-php/zx-goravel-website
 * $camel true => case camel,true => case snake
 */

Goravel::genAllTable();
Goravel::genAllRouter();

Goravel::genTable("table_name");
Goravel::genRouter("table_name");

/*
 * drogon 生成器,配合使用项目: https://github.com/zh7314/zx-drogon-website   https://gitee.com/open-php/zx-drogon-website
 * $camel true => case camel,true => case snake
 */

Drogon::genAllTable();
Drogon::genAllRouter();

Drogon::genTable("table_name");
Drogon::genRouter("table_name");

/*
 * laravelzx 定制化生成器
 */

LaravelSoftDelZx::genAllTable();
LaravelSoftDelZx::genAllRouter();

LaravelSoftDelZx::genTable("table_name");
LaravelSoftDelZx::genRouter("table_name");

```

6, If you use the default template directly you may need some helper code
They will be in `laravel/utils` in `tests/Tools`
Add in `composer.json`


```
"autoload": {
        "files": [
          "app/Utils/Function.php"
        ]
    }
```
7, If you don't like to use some auxiliary code, you can change the return code to
```
return response()->json(['code' => 200, 'msg' => '成功']);
```
So standard laravel writing, code check can also be changed
```
$where['weixin_phone'] = !empty($request->weixin_phone) ? (string)htmlspecialchars(trim($request->weixin_phone), ENT_QUOTES, "UTF-8") : '';
```
#### Custom template
1, extend `ZX\BaseGenerator`  
2, Implementation of abstract methods, referring to the `XXGenerator` method to achieve their own templates, general methods are provided

#### 问题反馈
QQ群：247823727  
博客：https://www.cnblogs.com/zx-admin/   
gitee：https://gitee.com/open-php/php-code-generator   
github：https://github.com/zh7314
