<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\AdminUserService;

class AdminUser extends CommonController
{
    /**
     * @title 用户列表
     * @param AdminUserService $adminUserService
     * @param array            $filter
     * @return array
     * @permission('user:list')
     */
    public function index(AdminUserService $adminUserService, array $filter = []): array
    {
        return $this->returnSuccessData($adminUserService->getList($filter));
    }

    /**
     * @title 用户详情
     * @param AdminUserService $adminUserService
     * @param int              $id
     * @return array
     * @permission('user:detail')
     */
    public function detail(AdminUserService $adminUserService, int $id): array
    {
        return $this->returnSuccessData($adminUserService->getDetail($id));
    }

    /**
     * @title add
     * @param AdminUserService $adminUserService
     * @param array            $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     * @permission('user:add')
     */
    public function add(AdminUserService $adminUserService, array $data): array
    {
        $adminUserService->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title edit
     * @param AdminUserService $adminUserService
     * @param int              $id
     * @param array            $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     * @permission('user:edit')
     */
    public function edit(AdminUserService $adminUserService, int $id = 0, array $data = []): array
    {
        if ($id) {
            return $this->returnSuccessData($adminUserService->getDetail($id));
        }

        $adminUserService->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title del
     * @param AdminUserService $adminUserService
     * @param int              $id
     * @return array
     * @throws \app\common\exception\ModelException
     * @permission('user:delete')
     */
    public function del(AdminUserService $adminUserService, int $id): array
    {
        $adminUserService->delete($id);
        return $this->returnSuccess();
    }

    /**
     * @title 用户筛选列表
     * @param AdminUserService $adminUserService
     * @param array            $filter
     * @return array
     */
    public function searchList(AdminUserService $adminUserService, array $filter = []): array
    {
        return $this->returnSuccessData($adminUserService->getSearchList($filter));
    }
}
