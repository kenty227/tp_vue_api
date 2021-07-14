<?php

namespace app\common\driver\paginator;

use think\Container;
use think\Paginator;

class Vue extends Paginator
{
    /**
     * 默认分页配置
     */
    const DEFAULT_CONFIG = [
        'var_list_rows' => 'limit',
        'list_rows' => 20
    ];

    /**
     * @title 获取分页配置
     * @return array
     */
    public static function getConfig(): array
    {
        $config = Container::get('config')->pull('paginate');
        return array_merge(self::DEFAULT_CONFIG, $config);
    }

    /**
     * @title 自动获取每页条数
     * @return int
     */
    public static function getListRows(): int
    {
        $config = self::getConfig();

        $page = Container::get('request')->param($config['var_list_rows']);

        if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
            return $page;
        }

        return $config['list_rows'];
    }

    /**
     * @title 渲染分页html
     * @return mixed|void
     */
    public function render()
    {
        // 前后端分离，无需渲染分页html，空实现即可
    }
}
