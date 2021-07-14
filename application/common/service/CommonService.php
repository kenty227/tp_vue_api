<?php

namespace app\common\service;

use app\common\exception\ServiceException;

class CommonService
{
    /**
     * @title 抛出服务异常
     * @param string $message
     * @param array  $data
     * @throws ServiceException
     */
    public static function exception(string $message, array $data = [])
    {
        throw new ServiceException($message, $data);
    }

    /**
     * @title 抛出非法请求异常
     * @param array $data
     * @throws ServiceException
     */
    public static function illegalRequestException(array $data = [])
    {
        throw new ServiceException(lang('RETURN_MESSAGE_ILLEGAL'), $data);
    }
}
