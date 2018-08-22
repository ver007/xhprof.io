To begin with, you need to install the XHProf extension. Refer to the [PHP documentation](http://www.php.net/manual/en/xhprof.setup.php) if you need assistance.

You will need to manually create the database and populate it with the provided scheme. The database scheme is located at `/setup/database.sql`.

Rename the `/xhprof/includes/config.inc.sample.php` to `/xhprof/includes/config.inc.php`. There are only two supported parameters.

* `xhprof_url` is the URL to the XHProf.io library.
* `pdo` is the PDO instance. This library uses [PDO](http://uk3.php.net/pdo) to handle all of the database operations.
* `enable` is a closure to control when enable data collection, return true means always enable

Some cases for reference:

Alway enable
``` php
'enable' => function() {
     return true;
}
```

Enable if url contents `debug` parameter:
``` php
'enable' => function() {
    if (!empty($_GET['debug'])) {
        return true;
    }
}
```

Enable for 1/100 probability
``` php
'enable' => function() {
    return rand(0, 100) === 42;
}
```

Enable for url path is `/`:
``` php
'enable' => function() {
    if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/') {
        return true;
    }
}
```


For XHProf.io to start collecting data, you need `/inc/inject.php` files included to every file of interest. The recommended approach is to update your `php.ini` configuration to automatically prepend and append these files.

    auto_prepend_file = /[absolute path to xhprof.io]/inc/inject.php

If you use nginx, you could configuration `auto_prepend_file` as a `fastcgi_param`

    fastcgi_param  PHP_VALUE "auto_prepend_file=/[absolute path to xhprof.io]/inc/inject.php";

If you are using PHP-FPM, then XHProf.io will utilise `fastcgi_finish_request` to hide any overhead related to data collection. There is nothing to worry about if you are not using PHP-FPM either, as the overhead is less than a few milliseconds.


### ZH-CN


xhprof.io修正版安装与使用

安装及配置方法如下，假设web服务器根目录为/opt/htdocs

```
cd /opt/htdocs
git clone https://github.com/EvaEngine/xhprof.io.git
cd xhprof.io/
composer install
cp xhprof/includes/config.inc.sample.php xhprof/includes/config.inc.php
vi xhprof/includes/config.inc.php
```

在MySQL中建立xhprof.io数据库，假设数据库名为`xhprof`，然后导入`xhprof/setup/database.sql`

配置文件`config.inc.php`中需要调整

* `'url_base' => 'http://localhost/xhprof.io/'`, 这是xhprof.io界面所在路径
* `'pdo' => new PDO('mysql:dbname=xhprof;host=localhost;charset=utf8', 'root', 'password')`, 根据MySQL实际情况调整配置
* `enable` 这是一个匿名函数，当匿名函数返回`true`时启用`xhprof`数据收集

通过配置`enable`项，就可以实现线上调试的需求，比如

始终开启`xhprof`

```php
 'enable' => function() {
      return true;
 }
 ```

1/100概率随机开启xhprof

```php
'enable' => function() {
  return rand(0, 100) === 1;
}
```

网页携带参数debug=1时开启xhprof

```php
'enable' => function() {
  return !empty($_GET['debug']);
}
```

网页URL为特定路径时开启

```php
'enable' => function() {
  return strpos($_SERVER['REQUEST_URI'], '/testurl') === 0;
}
```

最后按上文所述，在要配置的项目中包含`xhprof.io/inc/inject.php`即可。

线上环境操作时务必要胆大心细，如果没有结果尤其注意需要检查`xhprof`扩展是否安装。
