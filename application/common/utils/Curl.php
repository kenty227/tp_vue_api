<?php

namespace app\common\utils;

use think\facade\Log;

class Curl
{
    /**
     * @title 发送HTTP请求
     * @param string      $url    请求URL
     * @param array       $data   POST数据
     * @param string      $method 请求方法：GET/POST
     * @param bool        $encode 请求数据是否Json转换
     * @param bool        $decode 返回数据是否Json转换
     * @param array       $header 请求头信息
     * @param bool        $log    是否记录请求日志
     * @param string|null $error  错误信息
     * @return bool|mixed
     */
    public static function send(
        string $url,
        array $data = [],
        string $method = 'GET',
        bool $encode = true,
        bool $decode = true,
        array $header = [],
        bool $log = true,
        &$error = null
    ) {
        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        if (strtoupper($method) == 'POST') {
            $opts[CURLOPT_POST] = 1;

            // 转换JSON格式数据
            $encode && $data = json_encode($data, JSON_UNESCAPED_UNICODE);

            $opts[CURLOPT_POSTFIELDS] = $data;

            // 发送JSON数据
            if (is_string($data)) {
                array_push(
                    $header,
                    'Content-Length: ' . strlen($data),
                    'Content-Type: application/json; charset=utf-8'
                );
            }
        }

        // 设置请求头
        $header && $opts[CURLOPT_HTTPHEADER] = $header;

        // 初始化并执行curl请求
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        // 调试日志信息
        $logData = [
            'url' => $url,
            'request_data' => $data,
            'response_data' => $response
        ];

        // 请求错误
        if ($error) {
            $logData['curl_error'] = $error;
            Log::error("[ CURL ] " . var_export($logData, true));
            return false;
        }

        // 请求成功：记录调试日志信息
        $log && Log::info("[ CURL ] " . var_export($logData, true));

        return $decode ? json_decode($response, true) : $response;
    }
}
