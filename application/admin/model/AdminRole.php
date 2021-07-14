<?php

namespace app\admin\model;

use app\common\model\common\Common;

class AdminRole extends Common
{
    const COMMENT = '后台角色';
    protected $observerClass = event\Log::class;
    // 设置json类型字段
    protected $json = ['permission'];

    /**
     * @title findById
     * @param int $id
     * @return array
     */
    public static function findById(int $id): array
    {
        return self::getInfo('*', ['id' => $id]);
    }

    /**
     * @title 校验角色名称是否存在
     * @param string $name         角色名称
     * @param int    $ignoreRoleId 查询时忽略的角色ID
     * @return bool
     */
    public static function checkRoleNameIsExisted(string $name, int $ignoreRoleId = 0): bool
    {
        $map = [
            ['name', '=', $name]
        ];
        $ignoreRoleId && $map[] = ['id', '<>', $ignoreRoleId];

        return self::where($map)->count() ? true : false;
    }
}
