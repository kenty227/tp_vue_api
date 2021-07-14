<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\CacheService;

class Cache extends CommonController
{
    /**
     * @title index
     * @param CacheService $cacheService
     * @param array        $filter
     * @return array
     * @permission('cache:list')
     */
    public function index(CacheService $cacheService, array $filter = []): array
    {
        return $this->returnSuccessData($cacheService->getList($filter));
    }

    /**
     * @title del
     * @param CacheService $cacheService
     * @param string       $key
     * @return array
     * @permission('cache:delete')
     */
    public function del(CacheService $cacheService, string $key = ''): array
    {
        $cacheService->delete($key);
        return $this->returnSuccess();
    }
}
