<?php

namespace app\admin\service;

use app\common\service\CommonService;
use app\common\utils\Cache;

class CacheService extends CommonService
{
    /**
     * @title getList
     * @param array $filter
     * @return array
     */
    public function getList(array $filter = []): array
    {
        return Cache::getItemList(Cache::TAG_NAME);
    }

    /**
     * @title delete
     * @param string $key
     */
    public function delete(string $key = '')
    {
        if ($key) {
            Cache::rm($key);
        } else {
            Cache::clear(Cache::TAG_NAME);
        }
    }
}
