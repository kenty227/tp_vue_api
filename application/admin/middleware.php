<?php
// 模块中间件注册文件
return [
    app\admin\middleware\Authentication::class,
    app\admin\middleware\Authorization::class
];
