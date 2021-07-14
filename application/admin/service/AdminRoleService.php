<?php

namespace app\admin\service;

use app\common\service\CommonService;
use app\admin\model\AdminRole;
use app\common\utils\ModelFilter;

class AdminRoleService extends CommonService
{
    /**
     * @title getList
     * @return array
     */
    public function getList(): array
    {
        return AdminRole::findAllList('*', [], 'id asc');
    }

    /**
     * @title getSearchList
     * @param array $filter
     * @return array
     */
    public function getSearchList(array $filter = []): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['name', 'like', 'keyword']
        ], $filter);

        return AdminRole::findSearchList('name', 'id', $map);
    }

    /**
     * @title getDetail
     * @param int $id
     * @return array
     */
    public function getDetail(int $id): array
    {
        return AdminRole::getInfo('*', ['id' => $id]);
    }

    /**
     * @title save
     * @param array $data
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     */
    public function save(array $data)
    {
        if (empty($data) || $data['id'] == 1) {
            self::illegalRequestException();
        }

        if (AdminRole::checkRoleNameIsExisted($data['name'], $data['id'])) {
            $this->exception('角色名已存在');
        }

        AdminRole::saveSingleData($data);
    }

    /**
     * @title delete
     * @param int $id
     * @throws \app\common\exception\ModelException
     */
    public function delete(int $id)
    {
        AdminRole::deleteByPk($id);
    }
}
