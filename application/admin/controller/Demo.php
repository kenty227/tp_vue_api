<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\DemoService;

class Demo extends CommonController
{
    /**
     * @title index
     * @param DemoService $service
     * @return array
     * @permission('demo:list')
     */
    public function index(DemoService $service, array $filter): array
    {
        return $this->returnSuccessData($service->getList($filter));
    }

    /**
     * @title add
     * @param DemoService $service
     * @param array       $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @permission('demo:add')
     */
    public function add(DemoService $service, array $data): array
    {
        $service->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title edit
     * @param DemoService $service
     * @param int         $id
     * @param array       $data
     * @return array
     * @throws \app\common\exception\ModelException
     * @permission('demo:edit')
     */
    public function edit(DemoService $service, int $id = 0, array $data = []): array
    {
        if ($id) {
            return $this->returnSuccessData($service->getDetail($id));
        }
        $service->save($data);
        return $this->returnSuccess();
    }

    /**
     * @title del
     * @param DemoService $service
     * @param int|array   $id
     * @return array
     * @throws \app\common\exception\ModelException
     * @permission('demo:delete')
     */
    public function del(DemoService $service, $id): array
    {
        $service->delete($id);
        return $this->returnSuccess();
    }

    /**
     * @title import
     * @param DemoService $service
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \app\common\exception\ModelException
     * @permission('demo:import')
     */
    public function import(DemoService $service)
    {
        $service->import();
        return $this->returnSuccess();
    }

    /**
     * @title importTemplate
     * @param DemoService $service
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @permission('demo:import')
     */
    public function importTemplate(DemoService $service)
    {
        $service->importTemplate();
    }

    /**
     * @title export
     * @param DemoService $service
     * @param array       $filter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @permission('demo:export')
     */
    public function export(DemoService $service, array $filter = [])
    {
        $service->export($filter);
    }
}
