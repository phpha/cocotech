<?php
/**
 * 助手类
 */
declare(strict_types=1);

namespace Cocotech\Services;

class Helper
{
    /**
     * 错误配置
     * @var array
     */
    private static $error = [
        // 系统状态
        0 => '操作成功',
        1000 => '操作失败',
        // 请求参数
        1001 => '参数格式错误',
        1002 => '签名错误',
        1003 => '请求参数过期',
        // 响应数据
        1011 => '参数验签失败',
        1012 => 'RSA解密失败',
        1013 => 'AES解密失败',
    ];

    /**
     * HTTP请求
     * @param string $url
     * @param string $data
     * @return array
     */
    public static function request(string $url, $data): array
    {
        // 开始时间
        $start_time = microtime(true);
        // 初始化
        $handle = curl_init();
        // 配置
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        // 执行请求
        $result = curl_exec($handle);
        // 状态码
        $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        // 错误信息
        $err_no = curl_errno($handle);
        // 关闭资源
        curl_close($handle);
        // 结束时间
        $end_time = microtime(true);
        // 返回
        return [
            'err_no' => $err_no,
            'http_code' => $http_code,
            'used_time' => sprintf('%.0fms', ($end_time - $start_time) * 1000),
            'result' => $result
        ];
    }

    /**
     * 自定义返回
     * @param int $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    public static function return(int $code, array $data = [], string $msg = ''): array
    {
        // 返回
        return [
            'code' => $code,
            'msg' => empty($msg) ? self::$error[$code] : $msg,
            'data' => $data
        ];
    }

    /**
     * 生成唯一ID
     * @return string
     */
    public static function uniqueId(): string
    {
        // 微秒
        list ($micro, $second) = explode(' ', microtime());
        $id = sprintf('%s%06d%04d', date('YmdHis', intval($second)), substr($micro, 2, 6), mt_rand(1, 9999));
        // 返回
        return $id;
    }
}
