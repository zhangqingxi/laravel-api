<?php

namespace App\Services;

use App\Constants\CommonStatusCodes;
use App\Exceptions\AdminException;
use App\Exceptions\CustomException;
use Exception;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

/**
 * AES 加密服务类
 * @Auther Qasim
 * @date 2023/6/28
 */
class AesEncryptionService
{
    private string $cipher = 'AES-256-CBC';

    /**
     * 加密数据
     * @param string $data 需要加密的数据
     * @param string $key 加密密钥
     * @return string 加密后的数据
     * @throws Exception
     */
    public function encrypt(string $data, string $key): string
    {

        $key = base64_decode($key);

        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

        $encrypted = openssl_encrypt($data, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {

            throw new CustomException(message('encryption_failed'), CommonStatusCodes::ENCRYPTION_FAILED);
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * 解密数据
     * @param string $data 需要解密的数据
     * @param string $key 解密密钥
     * @return string 解密后的数据
     * @throws Exception
     */
    public function decrypt(string $data, string $key): string
    {

        $key = base64_decode($key);

        $decodedData = base64_decode($data);

        $iv = substr($decodedData, 0, openssl_cipher_iv_length($this->cipher));

        $ciphertext = substr($decodedData, openssl_cipher_iv_length($this->cipher));

        $decrypted = openssl_decrypt($ciphertext, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {

            throw new CustomException(message('decryption_failed'), CommonStatusCodes::DECRYPTION_FAILED);
        }

        return $decrypted;
    }


    /**
     * 生成AES密钥
     * @return string AES密钥
     * @throws RandomException
     */
    public function generateAesKey(): string
    {
        return bin2hex(random_bytes(16)); // 生成随机的16字节AES密钥
    }
}
