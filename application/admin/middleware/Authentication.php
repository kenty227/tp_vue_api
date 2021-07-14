<?php

namespace app\admin\middleware;

use think\Container;
use think\Request;
use app\admin\service\interfaces\UserLogin;
use app\common\exception\UnauthorizedException;

/**
 * 身份验证
 * Class Authentication
 * @package app\admin\middleware
 */
class Authentication
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @title handle
     * @param Request  $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($request->controller() != 'Base') {
            $this->checkAuthentication($request);
        }

        return $next($request);
    }

    /**
     * @title checkAuthentication
     * @param Request $request
     * @throws UnauthorizedException
     */
    private function checkAuthentication(Request $request)
    {
        if (!$request->header('token')) {
            throw new UnauthorizedException();
        }

        // 校验token
        try {
            Container::get(UserLogin::class)->checkLogin();
        } catch (\Exception $e) {
            throw new UnauthorizedException();
        }
    }
}
