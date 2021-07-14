<?php

namespace app\admin\model;

use app\common\model\common\Common;

class AdminUser extends Common
{
    const COMMENT = '后台用户';
    protected $observerClass = event\Log::class;

    /**
     * @title 校验用户名称是否存在
     * @param string $name         用户名称
     * @param int    $ignoreUserId 查询时忽略的用户ID
     * @return bool
     */
    public static function checkUserNameIsExisted(string $username, int $ignoreUserId = 0): bool
    {
        $map = [
            ['name', '=', $username]
        ];
        $ignoreUserId && $map[] = ['id', '<>', $ignoreUserId];

        return self::where($map)->count() ? true : false;
    }
}
