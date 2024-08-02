<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Torann\GeoIP\Facades\GeoIP;
use Torann\GeoIP\Location;

if (!function_exists('json')) {
    /**
     * @param int $code 接口状态码
     * @param string $message 接口信息
     * @param Collection|array|null $data 响应数据
     * @param int $status HTTP状态
     * @param array $headers 响应头
     * @return JsonResponse
     * @author Qasim
     * @time 2023/6/27 16:06
     */
    function json(int $code = 0, string $message = '', Collection|array|null $data = [], int $status = 200, array $headers = []): JsonResponse
    {

        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $status, $headers);
    }
}

if (!function_exists('json_log')) {
    /**
     * @param array $data 日志数据
     * @return string
     * @author Qasim
     * @time 2023/6/27 16:06
     */
    function json_log(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}


if (!function_exists('aes_encrypt')) {
    /**
     * @param string $data 加密数据
     * @param string $key AES秘钥
     * @return string
     * @author Qasim
     * @time 2023/6/28 15:40
     */
    function aes_encrypt(string $data, string $key): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }
}

if (!function_exists('aes_decrypt')) {

    /**
     * @param string $data 解密数据
     * @param string $key AES秘钥
     * @return string
     * @author Qasim
     * @time 2023/6/28 15:40
     */
    function aes_decrypt(string $data, string $key): string
    {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }
}


if (!function_exists('check_route')) {
    /**
     * 检查路由
     * @param Request $request
     * @param string $route 路由名称
     * @return bool
     */
    function check_route(Request $request, string $route = ''): bool
    {

        if ($route === 'admin') {

            return $request->is(config('admin.route_prefix') . '/*');
        } elseif ($route === 'api') {

            return $request->is('api/*');
        } elseif ($route === 'init') {

            return $request->is('init/*');
        }else{

            return false;
        }
    }
}


if (!function_exists('route_type')) {
    /**
     * 路由类型
     * @param Request $request
     * @return string
     */
    function route_type(Request $request): string
    {

        if(check_route($request, 'admin') || $request->is('init/admin')){

            return 'admin';
        }elseif(check_route($request, 'api') || $request->is('init/api')){

            return 'api';

        }else{

            return '';
        }
    }
}


if (!function_exists('message')) {

    /**
     *  输出信息
     *
     * @param string $key 语言包的键
     * @param string $route 路由名称
     * @param array $replace 替换的数据
     * @param string|null $locale 语言环境
     * @return string
     */
    function message(string $key, string $route = '', array $replace = [], string $locale = null): string
    {

        if($route){

            return __('message.' .$route . '.'. $key, $replace, $locale);
        }

        return __('message.' . $key, $replace, $locale);
    }
}



if (!function_exists('redis')) {

    /**
     * 获取Redis
     * @param string $routeName
     * @return Redis|Connection
     */
    function redis(string $routeName): Redis|Connection
    {
        return Redis::connection($routeName . '_cache');
    }
}


if (!function_exists('transform_null_to_empty_string')) {

    /**
     * 转换null为空字符串
     * @param array $data
     * @return array
     */
    function transform_null_to_empty_string(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = transform_null_to_empty_string($value);
            } elseif (is_null($value)) {
                $data[$key] = '';
            }
        }
        return $data;
    }
}


if (!function_exists('ip_address')) {

    /**
     * 获取IP地址信息
     * @param string $ip
     * @return array
     */
    function ip_address(string $ip = ''): array
    {
        try{

            $result =  GeoIP::getLocation($ip);

            if($result instanceof Location){

                return $result->toArray();
            }

            return [];
        }catch (Exception){

            return [];
        }
    }
}


if (!function_exists('format_bytes')) {

    /**
     * 文件大小格式化
     * @param $size
     * @return string
     */
    function format_bytes($size): string
    {
        if($size < 1024){
            return $size . 'B';
        }elseif($size < 1024 * 1024){
            return round($size / 1024, 2) . 'KB';
        }elseif($size < 1024 * 1024 * 1024){
            return round($size / (1024 * 1024), 2) . 'MB';
        }elseif($size < 1024 * 1024 * 1024 * 1024){
            return round($size / (1024 * 1024 * 1024), 2) . 'GB';
        }else{
            return round($size / (1024 * 1024 * 1024 * 1024), 2) . 'TB';
        }
    }
}


if(!function_exists('get_file_type_by_extension'))
{

    /**
     * 根据文件扩展名获取文件类型
     *
     * @param string $extension 文件扩展名
     * @return string 文件类型
     */
    function get_file_type_by_extension(string $extension): string
    {
        $fileTypes = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'],
            'doc' => ['doc', 'docx', 'pdf', 'txt', 'rtf'],
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'mkv'],
            // 添加更多类型和扩展名
        ];

        foreach ($fileTypes as $type => $extensions) {

            if (in_array(strtolower($extension), $extensions)) {

                return $type;
            }
        }

        return '';
    }
}
