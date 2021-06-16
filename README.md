# COCOTECH PHP-SDK

## 1、安装

```php
composer require phpha/cocotech
```

## 2、使用

```php
// 初始化
Cocotech\Kernel::init([
    // 测试环境
    'request_url' => 'https://test-api.cocotech.cn/',
    // 应用ID
    'app_id' => 'xxx',
    // 服务端公钥
    'public_key' => 'xxx',
    // 客户端私钥
    'private_key' => 'xxx',
    // 银行标识
    'bank_code' => 'pingan',
    // 账户类型
    'acct_type' => '2',
    // 外部请求流水号
    'out_request_id' => Cocotech\Services\Helper::uniqueId()
]);

// 调用接口
$result = Cocotech\Kernel::handle('v2/account/common/test', [
    'param1' => 'value1',
    'param2' => 'value2'
]);
```
