<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Builder;
/**
 * 请求日志模型
 *
 * @Auther Qasim
 * @date 2023/6/28
 */
class RequestLog extends Base
{

    protected $fillable = [
        'host', 'url', 'method', 'ip', 'headers', 'request_id', 'exception_data',
        'request_data', 'encrypt_request_data', 'response_data', 'encrypt_response_data', 'http_status'
    ];

    protected $casts = [
        'headers' => 'array',
        'request_data' => 'array',
        'exception_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * headers 入库
     * @param $value
     * @return void
     */
    public function setHeadersAttribute($value): void
    {
        $this->attributes['headers'] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * request_data 入库
     * @param $value
     * @return void
     */
    public function setRequestDataAttribute($value): void
    {
        $this->attributes['request_data'] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * response_data 入库
     * @param $value
     * @return void
     */
    public function setResponseDataAttribute($value): void
    {
        $this->attributes['response_data'] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * exception_data 入库
     * @param $value
     * @return void
     */
    public function setExceptionDataAttribute($value): void
    {
        $this->attributes['exception_data'] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @throws \Exception
     */
    public function getIpAddressAttribute(): string
    {

        if(!$this->last_login_at){

            return '';
        }

        if($this->ip === '127.0.0.1'){

            return '内网';
        }

        $ipInfo = ip_address($this->ip);

        if($ipInfo){

            return $ipInfo['country']  . '/' . $ipInfo['city'];
        }
        return '';
    }
}
