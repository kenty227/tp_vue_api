<?php

namespace app\admin\service;

use app\common\service\CommonService;
use app\admin\model\Demo;
use app\common\utils\ModelSort;
use app\common\utils\ModelFilter;
use app\admin\utils\ExcelImport;
use app\admin\utils\ExcelExport;
use app\common\model\TableDictionary;

class DemoService extends CommonService
{
    /**
     * @title getList
     * @param array $filter
     * @param bool  $paginate
     * @return array
     */
    public function getList(array $filter = [], bool $paginate = true): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['type', '=', 'type'],
            ['title', 'like', 'keyword'],
        ], $filter);

        if ($paginate) {
            return Demo::findPaginateList('*', $map, ModelSort::getSortExpression());
        }
        return Demo::findAllList('*', $map, ModelSort::getSortExpression());
    }

    /**
     * @title getDetail
     * @param int $id
     * @return array
     */
    public function getDetail(int $id): array
    {
        return Demo::getByPk($id);
    }

    /**
     * @title save
     * @param array $data
     * @throws \app\common\exception\ModelException
     */
    public function save(array $data)
    {
        Demo::saveSingleData($data);
    }

    /**
     * @title delete
     * @param int|array $id
     * @throws \app\common\exception\ModelException
     */
    public function delete($id)
    {
        Demo::deleteByPk($id);
    }

    /**
     * @title import
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \app\common\exception\ModelException
     */
    public function import()
    {
        $data = (new ExcelImport())->getData([
            '类型' => 'type',
            '标题' => 'title'
        ]);

        $v2k = TableDictionary::getValue2Key('demo', 'type');
        foreach ($data as &$v) {
            $v['type'] = $v2k[$v['type']] ?? 0;
        }

        Demo::createAllData($data);
    }

    /**
     * @title importTemplate
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function importTemplate()
    {
        $columnData = [
            '类型' => 'type',
            '标题' => 'title'
        ];

        $list = [
            [
                'type' => '测试类型_1',
                'title' => '测试1'
            ],
            [
                'type' => '测试类型_2',
                'title' => '测试2'
            ]
        ];

        (new ExcelExport(20))->commonExport($columnData, $list, 0);
    }

    /**
     * @title export
     * @param array $filter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(array $filter = [])
    {
        $list = $this->getList($filter, false);

        $k2v = TableDictionary::getKey2Value('demo', 'type');
        foreach ($list as &$v) {
            $v['type'] = $k2v[$v['type']] ?? '';
        }

        $columnData = [
            '类型' => 'type',
            '标题' => 'title',
            '内容' => 'content',
            '创建时间' => 'create_time',
            '更新时间' => 'update_time'
        ];

        (new ExcelExport(20))->commonExport($columnData, $list, 0);
    }
}
