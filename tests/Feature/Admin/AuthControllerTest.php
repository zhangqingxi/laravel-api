<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\Admin;
use App\Services\AesEncryptionService;
use App\Services\RsaEncryptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Random\RandomException;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{


    /**
     * 测试登录成功
     * @作者 Qasim
     * @日期 2023/6/28
     * @return void
     * @throws RandomException
     * @throws \Exception
     */
    public function testLoginSuccess(): void
    {


        $data = [
            'account' => 'admin',
            'password' => '123456789',
            'request_time' => date('Y-m-d H:i:s')
        ];

        $aesService = new AesEncryptionService();

        $aesKey = $aesService->generateAesKey();

        $encryptData = $aesService->encrypt(json_encode($data), $aesKey);

        //加密请求aesKey
        $rsaService = new RsaEncryptionService();

        $rsaService->setKeys('admin');

        $encryptAesKey = $rsaService->encrypt($aesKey);

        $headers = [
            'X-AES-KEY' => $encryptAesKey
        ];

        $data = ['data' => $encryptData];


        // 发送登录请求
        $response = $this->postJson('/admin6666/auth/login', $data, $headers);

        // 断言响应状态码
        $response->assertStatus(200);
        dd($data, $encryptAesKey, $response->json());
        // 断言响应结构
        $response->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'token',
                'user' => [
                    'id',
                    'account',
                    'nickname',
                    'email',
                    'phone',
                    'avatar',
                    'status',
                    'login_ip',
                    'last_login_at',
                    'login_attempts',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        // 断言响应数据
        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData['token']);
        $this->assertEquals($this->admin->account, $responseData['user']['account']);

    }

    /**
     * 测试登录失败
     * @作者 Qasim
     * @日期 2023/6/28
     * @return void
     * @throws \Exception
     */
    public function testLogout(): void
    {
        $data = "pvrtvPE0dQ/PeUGHH++5uDnkHHP0BVpok7qvmjMBFg4Oeq1YugXmYCnwYhbngR+kzfWDITdxu/LBXvXHgp7l9QOnxcJAZPQJ5J2ntnBRUxzpGA/TR90IuvW/dgtOIU8X";

        $encryptAesKey = "gjjs6smyyj8FXptfz0vEMDpiXVpQsMibW4Ox4Q3FP/iKPgyw8rL0/92HE1GpyTguqJWZbU4b3CyMlKs/JLPAKZ7QCK7lK7NptraN4B3eSjKoQblpwrexhWKYPYlspIl8KywzBQNuSmPMmxebnC62MglTHvzRLz1FNrTCg4E4Ix/dWM72TeRYsajcM+DDCColhOe/7TKZWLL/ScabcFhWpn2L5DdcDw5eU60t+rDlPxa0pbzvoDleA6kyCv0qDUmJRQyz5Jyh4n39Ath3gNcGUP/GjlF/zpyGPp4iaCrKugSLchpS0vSHQ1MjaSOO3wlBt0Z6BFxi7MLVIIxOZRWLZg==";

        $aesService = new AesEncryptionService();

        $rsaService = new RsaEncryptionService();

        $rsaService->setKeys('admin');

        $aesKey = $rsaService->decrypt($encryptAesKey);

        $data = $aesService->decrypt($data, $aesKey);

        $data = json_decode($data, true);

        // 发送错误的登录请求
        $response = $this->getJson('/admin6666/user/info', [
            'X-AES-KEY' => $encryptAesKey,
            "Authorization" => "Bearer " . $data['token']
        ]);

        dd($response->json(), [
            'X-AES-KEY' => $encryptAesKey,
            "Authorization" => "Bearer " . $data['token']
        ]);
        // 验证响应
        $response->assertStatus(401);
        $response->assertJson([
            'code' => 401,
            'message' => __('auth.failed'),
            'data' => null,
        ]);
    }

    public function testCurl()
    {
        $curl = curl_init();

        $headers =  [
                "CONTENT_LENGTH: 2",
              "CONTENT-TYPE: application/json",
              "ACCEPT: application/json",
              "X-AES-KEY: pEdkEQsgg7yV6ZmwpCFX16zvQS4Wy7REENU47M3aR9YkTwqUdQdVwmFL5MY0rhoUmDMdzMEtVuqBvtiCV6U2YHscC2I1KodwlxOtcmAOoxroYO9yK7zRGK3/WCjWSwK8fPD0LwoBtGQr9/Yq5yMKEvPh5xBl/Qs7nOdWsUg3L2nv8a0Jzj/oN7ef5V0edyKdUMGiW4PldL5dLb1ePKzgojBXDIUy+OSzdXcteffNaMXkqgg5Iawtp3oIFfJtx2rxuFqTaP7mPsWokAR3zMTYjNI947itrsI00CimuNnKvxNasRL1MIcMqCcckasN2hPgqXgDs260UYKqVkNtcayAiQ==",
              "AUTHORIZATION: Bearer 87|1WibGImSm2ISJqMgP7PMTIA37spwznPgvIKDVWi39f083794"
        ];


        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://local.web.cn/admin6666/auth/logout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'',
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        dd(json_decode($response, true));
        curl_close($curl);

    }
}
