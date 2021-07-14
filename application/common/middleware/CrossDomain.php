<?php
/**
 * 跨域请求处理中间件
 */

namespace app\common\middleware;

class CrossDomain
{
    /**
     * @title handle
     * @param \think\Request $request
     * @param \Closure       $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // 处理跨域请求
        if ($request->isAjax()) {
            // 允许ajax跨域请求
            header('Access-Control-Allow-Origin: ' . $request->header('Origin', '*'));
            // 保持ajax跨域请求时携带Cookie
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, sessionId, token');
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            // 处理OPTIONS
            if ($request->method() === 'OPTIONS') {
                // 本次预检请求的有效期，默认为20天
                header('Access-Control-Max-Age: 1728000');
                return response();
            }
        }

        return $next($request);
    }
}
