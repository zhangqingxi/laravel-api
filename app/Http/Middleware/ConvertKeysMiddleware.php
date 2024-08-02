<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;

/**
 * 请求字段驼峰转下划线、响应字段下换线转驼峰式中间件
 * @Auther Qasim
 * @date 2023/7/7
 */
class ConvertKeysMiddleware
{
    /**
     * 处理传入请求
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws CustomException
     */
    public function handle(Request $request, Closure $next): mixed
    {

        // 请求字段驼峰转下划线
        $request->replace($this->convertKeys($request->all(), 'camel_to_snake'));

        $response = $next($request);

        // 获取响应数据
        $responseData = $response->getData(true);

        // 响应字段下换线转驼峰式中间件
        $data = $this->convertKeys($responseData['data'], 'snake_to_camel');

        $responseData['data'] = $data;

        $response->setData($responseData);

        return $response;
    }


    /**
     * 递归地将数组的键名转换为驼峰式
     *
     * @param array $array
     * @param string $conversionType
     * @return array
     */
    private function convertKeys(array $array, string $conversionType): array
    {
        $camelCaseArray = [];

        foreach ($array as $key => $value) {
            $camelCaseKey = $this->convertKey($key, $conversionType);

            if (is_array($value)) {

                $value = $this->convertKeys($value, $conversionType);
            }

            $camelCaseArray[$camelCaseKey] = $value;
        }

        return $camelCaseArray;
    }

    /**
     * 根据指定的转换类型转换单个键名
     *
     * @param string $key
     * @param string $conversionType
     * @return string
     */
    private function convertKey(string $key, string $conversionType): string
    {
        switch ($conversionType) {
            case 'snake_to_camel':
                return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            case 'camel_to_snake':
                $key = preg_replace('/(.)(?=[A-Z])/u', '$1_', $key);
                return strtolower($key);
            default:
                return $key;
        }
    }
}
