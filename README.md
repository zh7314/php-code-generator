# php-code-generator

#### 介绍
基于php的代码生成器

#### 测试环境
php ^8.0

#### 支持范围
目前仅支持laravel，后续会支持webman，thinkphp等，也欢迎提交模板

#### composer
```
composer require zx/php-code-generator
```

#### 使用方法
1,代码生成器是基于渲染模板的方法，目前强调的是自定义的   
2,把`vender/zx/php-code-generator/src/Utils/`的文件复制`app/Utils`目录下  
3,因为每个人的代码习惯不同，所以可以根据模板适当修改，生成合适自己的代码  
4,每次生成前建议删除`tests`的目录，然后吧生成好的代码复制到对应的项目文件下，直接生成在项目目录
容易造成代码覆盖，所以推荐自己复制进去   
5,生成模板代码,在laravel容易的controller里面执行一下代码
```
use ZX\MysqlOperation;
use ZX\File;
use ZX\LaravelStd;
use ZX\LaravelZx;
use ZX\WebmanStd;
use ZX\WebmanZx;
use ZX\ThinkphpStd;
use ZX\ThinkphpZx;

$param = [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'benlai',
    'charset' => 'utf8',
    'user' => 'root',
    'pwd' => 'root'
];


MysqlOperation::setConnection($param);
//删除默认app路径文件
File::deldir('./' . LaravelZx::getAppPath() . DIRECTORY_SEPARATOR);

//测试
LaravelZx::generatorAllTable();

```
6,文件会在`public`，根据自己需要复制代码文件

7,如果你直接使用默认模板你可能需要一些辅助代码   
他们会在`vender/zx/php-code-generator/src/laravel/`的`utils`里面
在`composer.json`里面加上   
```
"autoload": {
        "files": [
          "app/Utils/Function.php"
        ]
    }
```
8,如果你不喜欢使用一些辅助代码，你可以把返回的代码改成
```
return response()->json(['code' => 200, 'msg' => '成功']);
```
这样标准laravel写法 ,代码检查也可以改成
```
$where['weixin_phone'] = !empty($request->weixin_phone) ? (string)htmlspecialchars(trim($request->weixin_phone), ENT_QUOTES, "UTF-8") : '';
```
#### 自定义模板
1,继承 `ZX\Generator`  
2,实现抽象方法，参照`LaravelGenerator` 的方法去实现自己的模板，通用的方法都有提供

#### 问题反馈
QQ群：247823727  
博客：https://www.cnblogs.com/zx-admin/   
gitee：https://gitee.com/open-php/php-code-generator   
github：https://github.com/zh7314
