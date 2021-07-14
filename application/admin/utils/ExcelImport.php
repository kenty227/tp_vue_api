<?php
/**
 * Excel 导入类
 * @require phpoffice/phpspreadsheet
 */

namespace app\admin\utils;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelImport
{
    /**
     * @var string 上传Excel文件
     */
    private $file;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadSheet;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private $workSheet;

    /**
     * ExcelImport constructor.
     * @param string $file     文件路径
     * @param bool   $readOnly 是否只读
     * @throws \Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function __construct(string $file = null, bool $readOnly = true)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        // 自动获取上传文件
        if (is_null($file)) {
            $file = (new Upload('file'))->getFileTmpPath('excel');
        }

        // 校验文件是否存在
        if (!file_exists($file)) {
            exception('请上传文件');
        }
        $this->file = $file;

        // 创建 Reader 对象
        $excelReader = IOFactory::createReader('Xlsx');
        if (!$excelReader->canRead($this->file)) {
            $excelReader = IOFactory::createReader('Xls');
            if (!$excelReader->canRead($this->file)) {
                $this->deleteFile();
                exception('上传的文件类型错误');
            }
        }

        // 设置只读（可大幅提升读取Excel效率）
        $readOnly && $excelReader->setReadDataOnly(true);

        // 读取 Excel，获取 Spreadsheet 对象
        $this->spreadSheet = $excelReader->load($file);
    }

    /**
     * @title getData
     * @param array $fieldData
     * @param int   $sheetIndex  活动表单下标
     * @param int   $startRow    数据起始行数
     * @param int   $startColumn 数据起始列数
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getData(array $fieldData, int $sheetIndex = 0, int $startRow = 2, int $startColumn = 1): array
    {
        $this->workSheet = $this->spreadSheet->getSheet($sheetIndex);

        // 表头格式检验
        $this->checkTableHeader(array_keys($fieldData), $startRow - 1);

        // 所有数据集
        $data = [];

        // 获取总行数
        $rowNum = $this->workSheet->getHighestRow();

        // 获取工作SHEET数据
        for ($row = $startRow; $row <= $rowNum; $row++) {
            // 数据起始列数数据为空则跳过
            if (empty($this->getValue($startColumn, $row))) continue;
            $rowData = [];
            // 当前列数
            $currentColumn = $startColumn;
            foreach ($fieldData as $field) {
                $rowData[$field] = $this->getValue($currentColumn++, $row);
            }
            $data[] = $rowData;
        }

        return $data;
    }

    /**
     * @title 表头格式检验
     * @param array $headerList
     * @param int   $headerRow
     * @throws \Exception
     */
    private function checkTableHeader(array $headerList, $headerRow = 1)
    {
        foreach ($headerList as $k => $name) {
            // 为空则不校验
            if (empty($name)) continue;

            if ($this->getValue($k + 1, $headerRow) != $name) {
                $this->deleteFile();
                exception('表头格式错误');
            }
        }
    }

    /**
     * @title 获取单元格数据值（无格式）
     * @param int $columnIndex 列号（从1开始）
     * @param int $row         行号
     * @return string
     */
    private function getValue(int $columnIndex, int $row): string
    {
        return trim($this->workSheet->getCellByColumnAndRow($columnIndex, $row)->getValue());
    }

    /**
     * @title 获取带公式单元格数据值
     * @param int $columnIndex 列号（从1开始）
     * @param int $row         行号
     * @return string
     */
    private function getFormattedValue(int $columnIndex, int $row): string
    {
        return trim($this->workSheet->getCellByColumnAndRow($columnIndex, $row)->getFormattedValue());
    }

    /**
     * @title 获取日期格式数据值
     * @param int    $columnIndex
     * @param int    $row
     * @param string $format
     * @return string
     * @throws \Exception
     */
    private function getDateValue(int $columnIndex, int $row, string $format = 'Y-m-d'): string
    {
        $cellValue = $this->getValue($columnIndex, $row);
        $timestamp = Date::excelToTimestamp($cellValue);
        return $format === '' ? $timestamp : date($format, $timestamp);
    }

    /**
     * @title 删除Excel文件
     * @param string $file
     */
    private function deleteFile(string $file = '')
    {
        $file ? unlink($file) : unlink($this->file);
    }
}
