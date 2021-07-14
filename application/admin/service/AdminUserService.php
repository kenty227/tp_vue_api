<?php

namespace app\admin\service;

use app\common\service\CommonService;
use app\admin\model\AdminUser;
use app\admin\utils\Password;
use app\common\utils\ModelFilter;

class AdminUserService extends CommonService
{
    /**
     * @title getList
     * @param array $filter
     * @return array
     */
    public function getList(array $filter = []): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['u.username|u.name|u.phone', 'like', 'keyword']
        ], $filter);

        $field = 'u.id, u.username, u.name, u.phone, u.last_login, u.is_lock, u.status, 
            u.role_id, r.name as role_name';
        $order = 'u.id asc';
        $alias = 'u';
        $join = ['admin_role r', 'u.role_id = r.id', 'LEFT'];

        $list = AdminUser::findPaginateList($field, $map, $order, null, $alias, $join);

        return $list;
    }

    /**
     * @title 获取用户筛选列表
     * @param array $filter
     * @return array
     */
    public function getSearchList(array $filter = []): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['name', 'like', 'keyword'],
            ['role_id', '=', 'role_id']
        ], $filter);

        return AdminUser::findSearchList('name', 'id', $map);
    }

    /**
     * @title 详情
     * @param int $id
     * @return array
     */
    public function getDetail(int $id): array
    {
        $data = AdminUser::getByPk($id);
        // 密码字段值置空
        $data['password'] = '';

        return $data;
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

        if (AdminUser::checkUserNameIsExisted($data['username'], $data['id'])) {
            $this->exception('账号已存在');
        }

        // 处理密码更新
        if (!empty($data['password'])) {
            $data['password'] = Password::encrypt($data['password']);
        } else {
            unset($data['password']);
        }

        AdminUser::saveSingleData($data);
    }

    /**
     * @title delete
     * @param int $id
     * @throws \app\common\exception\ModelException
     */
    public function delete(int $id)
    {
        AdminUser::deleteByPk($id);
    }
}
