# 金椰子官方 `SDK`

## 1、安装

```php
composer install phpha/cocotech
```

## 2、使用


```php
use Cocotech/Kernel;

// 初始化
Kernel::init([
    'request_url' => 'https://api.cocotech.cn/v1/',
    'app_id' => 'xxx',
    'public_key' => 'xxx',
    'private_key' => 'xxx'
]);

// 调用接口
$result = Kernel::handle('order/create', [
    'param1' => 'value1',
    'param2' => 'value2'
]);
```
