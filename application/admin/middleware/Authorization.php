<?php

namespace app\admin\middleware;

use think\App;
use think\Container;
use think\helper\Str;
use think\Request;
use app\admin\entity\LoginUser;
use app\common\exception\UnauthorizedException;
use app\common\utils\Annotation;

/**
 * 接口授权
 * Class Authorization
 * @package app\admin\middleware
 */
class Authorization
{
    /**
     * @title handle
     * @param Request  $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($request->controller() == 'Base' || $request->controller() == 'LoginUser') {
            return $next($request);
        }

        /** @var App $app */
        $app = Container::get('app');

        // 自动生成权限规则定义
        if ($app->isDebug()) {
            $permission = $this->getActionPermission($app, $request);
            if ($permission) {
                $this->checkApiPermission($permission);
            }
            return $next($request);
            // Console::call('optimize:permission');
        }

        // 加载权限规则文件
        $filename = $app->getRuntimePath() . 'build_permission.php';
        if (is_file($filename)) {
            $permissionList = include $filename;
        }

        if (empty($permissionList)) {
            throw new UnauthorizedException();
        }

        $module = $request->module();
        $controller = Str::studly($request->controller());

        // 控制器未设置权限：放行
        if (empty($permissionList[$module][$controller])) {
            return $next($request);
        }

        // 校验控制器/方法权限
        $controllerPermission = $permissionList[$module][$controller];
        if (is_string($controllerPermission)) {
            $permission = $controllerPermission;
        } elseif (is_array($controllerPermission)) {
            $permission = $controllerPermission[$request->action()] ?? null;
        }

        !is_null($permission) && $this->checkApiPermission($permission);

        return $next($request);
    }

    /**
     * @title 获取操作方法权限（读取注解）
     * @param App     $app
     * @param Request $request
     * @return string
     * @throws \ReflectionException
     */
    private function getActionPermission(App $app, Request $request): string
    {
        $namespace = $app->getNameSpace();
        $module = $request->module();
        $layer = $app->config('app.url_controller_layer');
        $controller = $request->controller();

        return Annotation::getStringValue(
            'permission',
            "{$namespace}\\{$module}\\{$layer}\\{$controller}",
            $request->action());
    }

    /**
     * @title checkApiPermission
     * @param string $permission
     * @throws UnauthorizedException
     */
    private function checkApiPermission(string $permission)
    {
        // 超管：放行
        if (LoginUser::isSuperAdministrator()) return;

        $userPermissionSet = LoginUser::getInstance()->getPermissionList();

        if (!in_array($permission, $userPermissionSet)) {
            throw new UnauthorizedException();
        }
    }
}
