<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    \app\common\command\Permission::class,
    \app\common\command\copy\PermissionCopyAll::class,
    \app\common\command\copy\PermissionCopyGroups::class,
    \app\common\command\copy\PermissionCopySubpoena::class,
    \app\common\command\copy\PermissionCopySubpoenaFront::class,
];
