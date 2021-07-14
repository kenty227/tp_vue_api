<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\AdminPermissionService;

class AdminPermission extends CommonController
{
    /**
     * @title index
     * @param AdminPermissionService $adminPermissionService
     * @param array                  $filter
     * @return array
     * @permission('permission:list')
     */
    public function index(AdminPermissionService $adminPermissionService, array $filter = []): array
    {
        return $this->returnSuccessData($adminPermissionService->getList($filter));
    }

    /**
     * @title parentList
     * @param AdminPermissionService $adminPermissionService
     * @param array                  $filter
     * @return array
     * @permission('permission:list')
     */
    public function parentList(AdminPermissionService $adminPermissionService, array $filter = []): array
    {
        return $this->returnSuccessData($adminPermissionService->getParentList($filter));
    }

    /**
     * @title add
     * @param AdminPermissionService $adminPermissionService
     * @param array                  $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     * @permission('permission:add')
     */
    public function add(AdminPermissionService $adminPermissionService, array $data): array
    {
        $adminPermissionService->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title edit
     * @param AdminPermissionService $adminPermissionService
     * @param array                  $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     * @permission('permission:edit')
     */
    public function edit(AdminPermissionService $adminPermissionService, array $data): array
    {
        $adminPermissionService->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title del
     * @param AdminPermissionService $adminPermissionService
     * @param int                    $id
     * @return array
     * @throws \app\common\exception\ModelException
     * @permission('permission:delete')
     */
    public function del(AdminPermissionService $adminPermissionService, int $id): array
    {
        $adminPermissionService->delete($id);
        return $this->returnSuccess();
    }
}
