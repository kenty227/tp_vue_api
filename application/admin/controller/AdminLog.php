<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\AdminLogService;

class AdminLog extends CommonController
{
    /**
     * @title index
     * @param AdminLogService $adminLogService
     * @param array           $filter
     * @return array
     * @permission('log:list')
     */
    public function index(AdminLogService $adminLogService, array $filter = []): array
    {
        return $this->returnSuccessData($adminLogService->getList($filter));
    }
}
