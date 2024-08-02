<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\Admin;
use App\Services\AesEncryptionService;
use App\Services\RsaEncryptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Random\RandomException;
use Tests\TestCase;

class EncryptControllerTest extends TestCase
{


    /**
     * 测试解密
     * @作者 Qasim
     * @日期 2023/6/29
     * @return void
     * @throws \Exception
     */
    public function testDecrypt(): void
    {

        $data = "hiYRYd69Gi3J7Sgv84RVQkluZmtvejJ5R3hqN1lUNTJOY3dMTmg2K0ozK2FCWjZOREhzTnFUNmZUSnJaeGlMYzVodHRLaUZFL3VCTmpuN29WcHJFYVo3U0ttRzhxOFVKbWxDVUxnZnkwNHB5YXVWMzBvazNaMmk3TEx5UGhnZG1iUzdQSHZGcVIxKzJSMzZqWkNiQkVhekxMRjQyVTRIMXI0eWRQZXgrTktGMjVxdDN0NXg3T21JMHdSalVDR2hWaTk1YWpRU1NWRGEzaEFoa1lHUkg1dUI5Z29Od2NWckplcVl4MU91MmU1bENkaWFHVzNWOXJVL09ZR3lOYm9rTm03akdOWmRGYkZsVnRmdkkwSy9EYVZSNDNJWTBETllLSVJaQmpWSXluNGlQVXJuWTl6RElScUg3Mi9EdVo1NTErUjJSSExFeVVVWG1ORmZYYm5xTFhVRFRJOTlPNjFLallnWWVsSFlGUFdRVi9kTlhGN2E0d09QSStXWkJueEJucC9EOFBRRFBQTVEvQlowbThyYUhzaUJzY29HMFFBTTUvNXlDZm9hZ3E3MWY0Q0ZCY3I0MkJ2cWVBVkxwQ3dTWjR4OUhrZEliWGJXZU1BcEd6Nm8yY29peFQ1azhUZ1FjcE1UMGpsUk1qNFhBNi8zeFBXNTFibFN2VVRzT3BxaGJwdUFZTVRSYVY0ZnVEQkY3TWFobFFFMUhqeURJYTNuWGVtM2lVdGFxdis5NVNnc3R5c3EyYWxTVUJPQTJMMTFra2RmYmVacmRWMTFFY1VRWDB5ZE0wRkhDUXNqWU44ZWM5SWlBYURQNlMzMEZiaTJQK2t5eTVmaFU4dHc5QVVmc2FIUEJ3WDkzQ0svUzRzc3cycGRCaDNSa1BIajJCT3NCb0hLRnY0d3M0elBIY3B0MDRjUW5SWVhFOHJOb1R0YlZsUDAyZmJmN3RFaWdzaW9nMWxvZEFXcGJqUmN4TkZ6NHh5Nko0c3ZrQVcvYjdIcFdya0MyeHppbi83TUJBQWttbWQ4R0pWYko0Sjd2M01xWGpZTXRyaHJ3OThwRWRLTERsb3NmakdrMGhYQXltV2tLd3F2Wi9RcmUwYktDcjVCVWFKZW9pM2RycWdBWmtTbjkyZGdsajVuaUJ2OXRwZlVuR2tMaTZnPT0=";

        $aesKey = "nL5e3OvgXIn8r2VVmQltasWIr6AUCA/RsgTeynIquczxgqnlBiN5QHA+8TXpdoa3ZBomEewyr1hvJl7wKVEOlyjbpPqQJkkTTxVOnvHy/cy54RtKKDv9/Iybs05sadLxt5tJcV3THJKLMeUBk0mt1m9vGpxrwP+w+tNGaadneV4GoEHDhYZKdBMRHhwOj6IsFD58jgY/omQLdYUPgJaHkvbwjxfpA8BhZlcpPz5V7JOEF3YGGhKRbUokKPyeT2kUtdUgHqiQM6JMlqpH+awWne0hCZrujYsH69dkLkNeBza98OMQx3tGSQ6ksamA0dceOPZVhgA74koNsty5ctQT4Q==";

        $aesService = new AesEncryptionService();

        $decryptedData = json_decode($aesService->decrypt($data, $aesKey), true);

        // 验证响应
        $response = $this->getJson("init/admin?data=/xPbsjSQKVf8OeHN+XabhHhZb1VFRkJuQSszajZoL0tONjN5TzdCQUduREVtQXhjTFo4cTUrN0lzekJ3RTdVOERteVM2dWZnWkE5eFdMM1k=", ['X-AES-KEY' => $aesKey]);

        // 断言响应状态码
        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true);

        $decryptedData2 = json_decode($aesService->decrypt($data['data'], $aesKey), true);

        // 验证解密后的数据
        $this->assertEquals($decryptedData['data'], $decryptedData2['data']);
    }

    /**
     * 测试加密
     * @作者 Qasim
     * @日期 2023/6/29
     * @return void
     * @throws \Exception
     */
    public function testEncrypt(): void
    {

        $data = [
            'account' => 'admin',
            'password' => '123456789',
        ];

        $aesKey = "f969dd1cabe78839080327ae59c41e71";

        $encryptAesKey1 = "w3E87Dy5KoTIDdppTDEFX7hVqB/lbuAEn1gPtvx2dImdOsWoQYcINqs9NWieLZtpCVsk/ZZdmpSDl1b3gIKHiLFzweP3WP0bk1v2XY9jszef+tUMyqrUqyblqsJK02IDHcjXfsiUlNcTx1nd2ZNVtkYjUFDY4nGT66l1PY6a2XR990VGdxFkuuKG77iWMibwMahDG6YMSXJvqtMTQY/0CRQWgVJNx2iekNPWr5QLtvV1xrDnp1GNfimyZwCFx5H6yC4XTJW5+ajCtPCW713v2ZGA2wBP4nKAnZMjy4AVJLftvAizRVPmDbbXn+RzuiHfuUgWClIH1VsSUz9joL2ttQ==";

        $aesService = new AesEncryptionService();

        $rsaService = new RsaEncryptionService();

        $rsaService->setKeys('admin');

        $this->assertEquals($aesKey, $rsaService->decrypt($encryptAesKey1));

        //加密data
        $decryptedData = $aesService->encrypt(json_encode($data), $aesKey);

        dd($decryptedData);

        //"7a2c3cede52f2eab9e09b12a9220c712" // tests\Feature\Admin\EncryptControllerTest.php:78
        //"7a2c3cede52f2eab9e09b12a9220c712"
//        dd($rsaService->decrypt('LwWfkFS01U/jvXCG3M2v6WCwWYpJ/YPvck46ULTRqraRAvu1uTiDknb+I0jvJDBzFXlc4xUQr1E9OYovBbiymaCBmhrdYQoG6d2WAgteWKamzkMYr3975Qc9Pb/Saq7JCHycbhZ4dZzy7FwZGPG/dicELZjgoM7H4SQYCJG4ht32B+RQCU2/wLRm3mhDzH26NxyRVesbBprn6+ZrBPsetmmrm/rQXvN4E679lS/ycS1zfTZMFdStYW1MkHCvPW85uR3X17s5b/PswrxXbqGPX6cXYc/FERIHFRf4+mMiAkIAqmeOZM+uf7jn8XWSBcNz1j3k1BjCTm/e2YLh7ioEQw=='));





        dd($decryptedData);

        dd($aesKey);


        dd($decryptedData);
        // 验证响应
        $response = $this->getJson("init/admin?data=/xPbsjSQKVf8OeHN+XabhHhZb1VFRkJuQSszajZoL0tONjN5TzdCQUduREVtQXhjTFo4cTUrN0lzekJ3RTdVOERteVM2dWZnWkE5eFdMM1k=", ['X-AES-KEY' => $aesKey]);

        // 断言响应状态码
        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true);

        $decryptedData2 = json_decode($aesService->decrypt($data['data'], $aesKey), true);

        // 验证解密后的数据
        $this->assertEquals($decryptedData['data'], $decryptedData2['data']);
    }
}
