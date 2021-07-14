<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 模块容器绑定定义
return [
    // 绑定标识
    'user_login' => app\admin\service\UserLoginService::class,
    'captcha' => app\admin\utils\Captcha::class,
    // 绑定接口依赖注入的实现类：接口 => （ 标识 => ） 实现类
    app\admin\service\interfaces\UserLogin::class => 'user_login'
];
