<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\AdminRoleService;

class AdminRole extends CommonController
{
    /**
     * @title index
     * @param AdminRoleService $adminRoleService
     * @return array
     * @permission('role:list')
     */
    public function index(AdminRoleService $adminRoleService): array
    {
        return $this->returnSuccessData($adminRoleService->getList());
    }

    /**
     * @title add
     * @param AdminRoleService $adminRoleService
     * @param array            $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     * @permission('role:add')
     */
    public function add(AdminRoleService $adminRoleService, array $data): array
    {
        $adminRoleService->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title edit
     * @param AdminRoleService $adminRoleService
     * @param array            $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     * @permission('role:edit')
     */
    public function edit(AdminRoleService $adminRoleService, array $data): array
    {
        $adminRoleService->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title del
     * @param AdminRoleService $adminRoleService
     * @param int              $id
     * @return array
     * @throws \app\common\exception\ModelException
     * @permission('role:delete')
     */
    public function del(AdminRoleService $adminRoleService, int $id): array
    {
        $adminRoleService->delete($id);
        return $this->returnSuccess();
    }

    /**
     * @title searchList
     * @param AdminRoleService $adminRoleService
     * @param array            $filter
     * @return array
     */
    public function searchList(AdminRoleService $adminRoleService, array $filter = []): array
    {
        return $this->returnSuccessData($adminRoleService->getSearchList($filter));
    }
}
