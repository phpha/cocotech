<?php
/**
 * AES|加密|解密
 */
declare(strict_types=1);

namespace App\Services\Helper\Encrypt;

class Aes
{
    /**
     * 加密
     * @param string $data
     * @param string $key
     * @return string|bool
     */
    public static function encrypt(string $data, string $key)
    {
        // 初始化向量
        $iv = str_repeat(chr(0), 16);
        // 加密
        $result = openssl_encrypt($data, 'AES-256-CBC', $key, 1, $iv);
        // 返回
        return $result === false ? false : base64_encode($result);
    }

    /**
     * 解密
     * @param string $data
     * @param string $key
     * @return string|bool
     */
    public static function decrypt(string $data, string $key)
    {
        // 初始化向量
        $iv = str_repeat(chr(0), 16);
        // 解密
        $result = openssl_decrypt(base64_decode($data), 'AES-256-CBC', $key, 1, $iv);
        // 返回
        return $result === false ? false : $result;
    }
}
