<?php

namespace app\common\exception;

use Exception;
use think\Container;
use think\Response;
use think\exception\Handle;
use think\exception\HttpException;

/**
 * 自定义错误处理
 * Class JsonThinkExceptionHandle
 * @package app\common\exception
 */
class JsonThinkExceptionHandle extends Handle
{
    /**
     * @access protected
     * @param Exception $exception
     * @return Response
     */
    protected function convertExceptionToResponse(Exception $exception)
    {
        // 收集异常数据
        $traces = $exception->getTrace();

        // $untraceFuncs = ['exec', 'module'];
        // foreach ($traces as $index => $trace) {
        //     if (in_array($trace['function'], $untraceFuncs)) {
        //         $traces[$index]['args'] = [];
        //     }
        // }

        if (Container::get('app')->isDebug()) {
            // 调试模式，获取详细的错误信息
            $data = [
                'name' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $this->getMessage($exception),
                'trace' => $traces,
                'code' => $this->getCode($exception),
                'source' => $this->getSourceCode($exception),
                'datas' => $this->getExtendData($exception),
                'tables' => [
                    'GET Data' => $_GET,
                    'POST Data' => $_POST,
                    'Files' => $_FILES,
                    'Cookies' => $_COOKIE,
                    'Session' => isset($_SESSION) ? $_SESSION : []
                ],
            ];
        } else {
            // 部署模式仅显示 Code 和 Message
            $data = [
                'code' => $this->getCode($exception),
                'message' => $this->getMessage($exception),
            ];

            if (!Container::get('app')->config('show_error_msg')) {
                // 不显示详细错误信息
                $data['message'] = Container::get('app')->config('error_message');
            }
        }

        // 保留一层
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        $data['echo'] = ob_get_clean();

        $response = Response::create($data, 'json');

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $response->header($exception->getHeaders());
        }

        if (!isset($statusCode)) {
            $statusCode = 500;
        }
        $response->code($statusCode);

        return $response;
    }

    /**
     * 获取异常扩展信息
     * 用于非调试模式html返回类型显示
     * @access protected
     * @param \Exception $exception
     * @return array                 异常类定义的扩展数据
     */
    protected function getExtendData(Exception $exception)
    {
        $data = [];

        if ($exception instanceof \think\Exception) {
            $data = $exception->getData();
        }

        // 隐藏数据库配置信息
        if ($exception instanceof \think\exception\PDOException) {
            unset($data['Database Config']);
        }
        if ($exception instanceof \think\exception\DbException) {
            unset($data['Database Config']);
        }

        return $data;
    }
}
