<?php
/**
 * 核心类
 */
declare(strict_types=1);

namespace Cocotech;

use Cocotech\Services\Aes;
use Cocotech\Services\Helper;
use Cocotech\Services\Rsa;

class Kernel
{
    /**
     * 配置
     * @var array
     */
    private static $config = [];

    /**
     * 初始化
     * @param array $config
     * @return void
     */
    public static function init(array $config)
    {
        // 赋值
        self::$config = $config;
    }

    /**
     * 请求接口
     * @param string $api
     * @param array $param
     * @return array
     */
    public static function handle(string $api, array $param): array
    {
        // 校验参数
        if (empty(self::$config['app_id']) || empty(self::$config['out_request_id']) || empty(self::$config['bank_code']) || empty(self::$config['acct_type'])
            || empty(self::$config['public_key']) || empty(self::$config['private_key']) || empty(self::$config['request_url'])) {
            return Helper::return(1001);
        }
        // 格式化
        $param = self::format($param);
        // 请求地址
        $url = sprintf('%s%s', self::$config['request_url'], $api);
        // 发送请求
        $result = Helper::request($url, $param);
        // 请求失败
        if ($result['err_no'] !== 0 || empty($result['result'])) {
            return Helper::return(1002);
        }
        // 转换格式
        $data = json_decode($result['result'], true);
        // 转换失败
        if (empty($data['data']['sign'])) {
            return Helper::return(1003);
        }
        // 验证响应数据
        $result = self::verify($data['data']);
        // 返回
        return $result;
    }

    /**
     * 格式化请求参数
     * @param array $param
     * @return string
     */
    private static function format(array $param): string
    {
        // 请求参数
        $param = json_encode($param);
        // 随机秘钥
        $secret = md5(Helper::uniqueId());
        // 格式化
        $format = [
            'appId' => self::$config['app_id'],
            'outRequestId' => self::$config['out_request_id'],
            'bankCode' => self::$config['bank_code'],
            'acctType' => self::$config['acct_type'],
            'biz' => Aes::encrypt($param, $secret),
            'key' => Rsa::encrypt($secret, self::$config['public_key']),
            'timestamp' => time()
        ];
        // 排序
        ksort($format);
        // 拼接
        $str = rawurldecode(http_build_query($format));
        // 加签
        $format['sign'] = Rsa::sign($str, self::$config['private_key']);
        // 返回
        return json_encode($format);
    }

    /**
     * 验证业务响应参数
     * @param array $param
     * @return array
     */
    private static function verify(array $param): array
    {
        // 原签名
        $sign = $param['sign'];
        // 过滤
        unset($param['sign']);
        // 排序
        ksort($param);
        // 拼接
        $str = rawurldecode(http_build_query($param));
        // 验签
        $result = Rsa::verify($str, self::$config['public_key'], $sign);
        // 验签失败
        if (!$result) {
            return Helper::return(1011);
        }
        // RSA解密
        $secret = Rsa::decrypt($param['key'], self::$config['private_key']);
        // 解密失败
        if ($secret === false) {
            return Helper::return(1012);
        }
        // AES解密
        $data = Aes::decrypt($param['biz'], $secret);
        // 解密失败
        if ($data === false) {
            return Helper::return(1013);
        }
        // 转换格式
        $data = json_decode($data, true);
        // 返回
        return Helper::return(0, $data);
    }
}
