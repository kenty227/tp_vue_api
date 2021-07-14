<?php

namespace app\admin\model;

use app\common\model\common\Common;

class AdminPermission extends Common
{
    const COMMENT = '后台权限';
    protected $observerClass = event\Log::class;
    protected $json = ['permission'];

    /**
     * @title setPermissionAttr
     * @param $value
     * @return array
     */
    public function setPermissionAttr($value): array
    {
        if (is_string($value)) {
            return explode(',', $value);
        }
        if (is_array($value)) {
            return $value;
        }
        return [];
    }
}
