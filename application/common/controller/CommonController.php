<?php

namespace app\common\controller;

use think\Controller;
use app\common\exception\ControllerException;

class CommonController extends Controller
{
    /**
     * 返回状态码
     */
    const CODE = [
        'SUCCESS' => 200,
        'ERROR' => 400,
        'UNAUTHORIZED' => 401,
        'ILLEGAL' => 403
    ];

    /**
     * @title 抛出控制器异常
     * @param string $message
     * @param array  $data
     * @throws ControllerException
     */
    public static function exception(string $message, array $data = [])
    {
        throw new ControllerException($message, $data);
    }

    /**
     * @title 返回成功
     * @param string|null $msg
     * @param array|null  $data
     * @return array
     */
    public static function returnSuccess(string $msg = null, array $data = null): array
    {
        return self::returnJson(self::CODE['SUCCESS'], $msg, $data);
    }

    /**
     * @title 返回成功数据
     * @param array $data
     * @return array
     */
    public static function returnSuccessData(array $data): array
    {
        return self::returnJson(self::CODE['SUCCESS'], null, $data);
    }

    /**
     * @title 返回错误
     * @param string|null $msg
     * @param array|null  $data
     * @return array
     */
    public static function returnError(string $msg = null, array $data = null): array
    {
        return self::returnJson(self::CODE['ERROR'], $msg, $data);
    }

    /**
     * @title 返回json格式响应数据
     * @param int|null    $code 状态码
     * @param string|null $msg  返回消息
     * @param array|null  $data 返回数据
     * @return array
     */
    public static function returnJson(int $code = null, string $msg = null, array $data = null): array
    {
        is_null($code) && $code = self::CODE['SUCCESS'];

        if (is_null($msg)) {
            switch ($code) {
                case self::CODE['SUCCESS']:
                    $msg = lang('RETURN_MESSAGE_SUCCESS');
                    break;
                case self::CODE['ERROR']:
                    $msg = lang('RETURN_MESSAGE_ERROR');
                    break;
                case self::CODE['ILLEGAL']:
                    $msg = lang('RETURN_MESSAGE_ILLEGAL');
                    break;
                case self::CODE['UNAUTHORIZED']:
                    $msg = lang('RETURN_MESSAGE_UNAUTHORIZED');
                    break;
                default:
                    $msg = lang('RETURN_MESSAGE_UNKNOWN');
            }
        }

        is_null($data) && $data = [];

        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
    }
}
