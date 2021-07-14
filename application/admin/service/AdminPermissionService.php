<?php

namespace app\admin\service;

use app\common\service\CommonService;
use app\admin\model\AdminPermission;
use app\common\utils\ModelFilter;
use app\common\utils\Tree;

class AdminPermissionService extends CommonService
{
    /**
     * @title getMenuAndPermissionInfo
     * @param array $ids
     * @param bool  $all
     * @return array
     */
    public static function getMenuAndPermissionInfo(array $ids, bool $all = false): array
    {
        $map = [];
        $all || $map[] = ['id', 'in', $ids];

        $list = AdminPermission::findAllList(
            'id, pid, path, name as title, component, icon, hidden, permission, type',
            $map,
            'pid asc, sort asc, id asc'
        );

        $permission = [];
        foreach ($list as $k => $v) {
            if ($v['permission'] && is_array($v['permission'])) {
                $permission = array_merge($permission, $v['permission']);
            }
            if ($v['type'] == 30) {
                unset($list[$k]);
            }
        }

        return [
            'permission' => array_values(array_unique($permission)),
            'menu' => Tree::listToTree($list)
        ];
    }

    /**
     * @title getList
     * @param array $filter
     * @return array
     */
    public function getList(array $filter = []): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['title', 'like', 'keyword'],
            ['selectable', '=', 'selectable'],
        ], $filter);

        $list = AdminPermission::findAllList('*', $map, 'pid asc, sort asc, id asc');
        foreach ($list as &$v) {
            $v['permission'] = implode(',', $v['permission']);
        }

        return Tree::listToTree($list);
    }

    /**
     * @title 父级列表
     * @param array $filter
     * @return array
     */
    public function getParentList(array $filter = []): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['title', 'like', 'keyword']
        ], $filter);

        $list = AdminPermission::findAllList('id, title, pid', $map, 'pid asc, sort asc, id asc');

        return Tree::listToTree($list);
    }

    /**
     * @title save
     * @param array $data
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     */
    public function save(array $data)
    {
        if (empty($data)) {
            self::illegalRequestException();
        }

        AdminPermission::saveSingleData($data);
    }

    /**
     * @title delete
     * @param int $id
     * @throws \app\common\exception\ModelException
     */
    public function delete(int $id)
    {
        AdminPermission::deleteByPk($id);
    }
}
