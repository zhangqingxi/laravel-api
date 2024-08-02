<?php

namespace App\Services;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use Exception;

/**
 * RSA 加密服务类
 * @Auther Qasim
 * @date 2023/6/28
 */
class RsaEncryptionService
{
    /**
     * 公钥
     * @var string
     */
    private string $publicKey;

    /**
     * 私钥
     * @var string
     */
    private string $privateKey;

    public function __construct()
    {

        // 默认读取 API 模式的公钥和私钥
//        $this->setKeys('api');
    }

    /**
     * 获取公钥
     * @return string 公钥
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * 获取公钥
     * @param string $key 配置文件名
     * @return void 公钥
     */
    public function setKeys(string $key): void
    {
        $this->publicKey = file_get_contents(config($key . '.encryption.rsa_public_key'));
        $this->privateKey = file_get_contents(config($key . '.encryption.rsa_private_key'));
    }

    /**
     * 使用公钥加密数据
     * @param string $data 需要加密的数据
     * @return string 加密后的数据
     * @throws Exception
     */
    public function encrypt(string $data): string
    {
        $encrypted = null;

        if (!openssl_public_encrypt($data, $encrypted, $this->publicKey)) {

            //数据加密失败
            throw new CustomException(message('encryption_failed'), CommonStatusCodes::ENCRYPTION_FAILED);
        }

        return base64_encode($encrypted);
    }

    /**
     * 使用私钥解密数据
     * @param string $data 需要解密的数据
     * @return string 解密后的数据
     * @throws Exception
     */
    public function decrypt(string $data): string
    {
        $decodedData = base64_decode($data);
        $decrypted = null;

        if (!openssl_private_decrypt($decodedData, $decrypted, $this->privateKey)) {

            throw new CustomException(message('decryption_failed'), CommonStatusCodes::DECRYPTION_FAILED);
        }

        return $decrypted;
    }
}
