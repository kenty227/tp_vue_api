<?php
/**
 * Excel 导出类
 * @require phpoffice/phpspreadsheet
 */

namespace app\admin\utils;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class ExcelExport
{
    // 默认列宽
    public $columnWidth;
    // 默认行高
    public $rowHeight;
    // 表头
    public $columnTitle = [];
    // 表数据字段
    public $columnField = [];
    // 最后一列列号
    private $lastColumn = 1;
    // 最后一行行号
    private $lastRow = 1;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadSheet;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private $workSheet;

    /**
     * ExcelExport constructor.
     * @param int  $columnWidth         默认列宽
     * @param int  $rowHeight           默认行高（0：不设置）
     * @param bool $setDefaultWorkSheet 是否设置默认工作表
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function __construct(int $columnWidth = 20, int $rowHeight = 0, $setDefaultWorkSheet = true)
    {
        set_time_limit(0);

        // 实例化 Spreadsheet
        $this->spreadSheet = new Spreadsheet();

        // 设置单元格格式
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // 水平居中
                'vertical' => Alignment::VERTICAL_CENTER // 垂直居中
            ],
            'font' => [
                'name' => '宋体',
                'size' => 10
            ]
        ];
        $this->spreadSheet->getDefaultStyle()->applyFromArray($styleArray);

        // 设置默认列宽
        $columnWidth && $this->columnWidth = $columnWidth;
        // 预设默认行高值（行高不能统一设置，需逐行设置）
        $rowHeight && $this->rowHeight = $rowHeight;
        // 设置默认工作SHEET
        $setDefaultWorkSheet && $this->setActiveSheet();
    }

    /**
     * @title 设置工作SHEET
     * @param int    $index
     * @param string $title
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setActiveSheet($index = 0, $title = '')
    {
        $index && $this->spreadSheet->createSheet();
        // 设置工作SHEET
        $this->workSheet = $this->spreadSheet->setActiveSheetIndex($index);
        // 设置SHEET名
        $title && $this->workSheet->setTitle($title);

        // 设置默认列宽
        $this->columnWidth && $this->workSheet->getDefaultColumnDimension()->setWidth($this->columnWidth);
    }

    /**
     * @title commonExport
     * @param array         $columnData
     * @param array         $data
     * @param int           $type     导出类型（0：无序号，1：带序号）
     * @param string        $filename
     * @param \Closure|null $function 自定义扩展操作函数
     */
    public function commonExport(array $columnData, array $data, int $type = 1, string $filename = '', \Closure $function = null)
    {
        $columnTitle = array_keys($columnData);
        $columnField = array_values($columnData);

        // 带序号导出，添加序号列
        if ($type == 1) {
            array_unshift($columnTitle, '序号');
        }

        // 设置列表头
        $this->setColumnTitle($columnTitle, 1);
        // 设置列字段
        $this->setColumnField($columnField);

        // 设置表数据
        $startRow = 2;
        $this->setCellData($data, $startRow, $type);

        // 调用自定义扩展操作函数
        if (!is_null($function)) {
            call_user_func($function, $this);
        }

        // 下载
        $this->download($filename);
    }

    /**
     * @title commonExportSave
     * @param array         $columnData
     * @param array         $data
     * @param int           $type
     * @param string        $filename
     * @param \Closure|null $function
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function commonExportSave(
        array $columnData,
        array $data,
        int $type = 1,
        string $filename = '',
        \Closure $function = null
    ): string {
        $columnTitle = array_keys($columnData);
        $columnField = array_values($columnData);

        // 带序号导出，添加序号列
        if ($type == 1) {
            array_unshift($columnTitle, '序号');
        }

        // 设置列表头
        $this->setColumnTitle($columnTitle, 1);
        // 设置列字段
        $this->setColumnField($columnField);

        // 设置表数据
        $startRow = 2;
        $this->setCellData($data, $startRow, $type);

        // 调用自定义扩展操作函数
        if (!is_null($function)) {
            call_user_func($function, $this);
        }

        // 保存
        return $this->save($filename);
    }

    /**
     * @title 设置列表头
     * @param array $columnTitle
     * @param int   $row
     */
    public function setColumnTitle(array $columnTitle = [], int $row = 1)
    {
        $columnTitle && $this->columnTitle = $columnTitle;

        foreach ($this->columnTitle as $index => $title) {
            // 标题为加粗宋体12号
            $this->workSheet->getStyleByColumnAndRow($index + 1, $row)
                ->getFont()
                ->setSize(12)
                ->setBold(true);
            $this->workSheet->setCellValueByColumnAndRow($index + 1, $row, $title);
        }

        // 设置最后一列列号
        $this->lastColumn = count($this->columnTitle);

        // 设置默认行高
        $this->setRowHeight($row);
    }

    /**
     * @title 设置列字段
     * @param array $columnField
     */
    public function setColumnField(array $columnField)
    {
        $this->columnField = $columnField;
    }

    /**
     * @title 写入表数据
     * @param array $data 单元格数据
     * @param int   $row  起始行数
     * @param int   $type 导出类型（0：无序号，1：带序号）
     */
    public function setCellData(array $data, int &$row, int $type = 0)
    {
        $diff = $row - 1;
        foreach ($data as $v) {
            // 设置序号
            $type && $this->workSheet->setCellValueByColumnAndRow(1, $row, $row - $diff);
            // 写入行数据
            foreach ($this->columnField as $index => $field) {
                $value = $v[$field] ?? null;
                if (is_null($value)) {
                    continue;
                }

                // 数字字符串，设置单元格格式为文本（处理数字自动转化为科学计算法的问题）
                if (is_numeric($value)) {
                    $this->workSheet->setCellValueExplicitByColumnAndRow($type + $index + 1, $row, $value, DataType::TYPE_STRING);
                } else {
                    // 包含换行内容，设置单元格格式
                    if (strpos($value, "\n") !== false) {
                        $value = rtrim($value, "\n"); // 去除最后换行符
                        $this->workSheet
                            ->getStyleByColumnAndRow($type + $index + 1, $row)
                            ->getAlignment()
                            ->setWrapText(true);
                    }
                    $this->workSheet->setCellValueByColumnAndRow($type + $index + 1, $row, $value);
                }
            }
            // 设置默认行高
            $this->setRowHeight($row);
            $row++;
        }
        $this->lastRow = $row - 1;
    }

    /**
     * @title 设置边框
     * @param int $lastRow    最后一行行号
     * @param int $lastColumn 最后一列列号
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setBorder(int $lastRow = 0, int $lastColumn = 0)
    {
        !$lastRow && $lastRow = $this->lastRow;
        !$lastColumn && $lastColumn = $this->lastColumn;
        $this->workSheet->getStyleByColumnAndRow(1, 1, $lastColumn, $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
    }

    /**
     * @title 设置列宽
     * @param int $columnIndex
     * @param int $width
     */
    public function setColumnWidth(int $columnIndex, int $width)
    {
        $this->workSheet->getColumnDimensionByColumn($columnIndex)->setWidth($width);
    }

    /**
     * @title 设置单列水平对齐方式
     * @param string   $column    列名
     * @param string   $alignment 水平对齐方式（默认左对齐）
     * @param int      $startRow  起始行数（默认第2行）
     * @param int|null $endRow    最尾行数（默认数据最后行）
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setColumnHorizontal(string $column, string $alignment = Alignment::HORIZONTAL_LEFT, int $startRow = 2, int $endRow = null)
    {
        is_null($endRow) && $endRow = $this->lastRow;
        if ($startRow > $endRow) {
            return;
        }
        $this->workSheet
            ->getStyle("{$column}{$startRow}:$column{$endRow}")
            ->getAlignment()
            ->setHorizontal($alignment);
    }

    /**
     * @title 设置行高
     * @param int $row
     * @param int $height
     * @return void
     */
    public function setRowHeight(int $row, int $height = 0)
    {
        if (!$height) {
            // 未设置默认行高，则不设置
            if (!$this->rowHeight) {
                return;
            }
            // 使用默认行高
            $height = $this->rowHeight;
        }
        $this->workSheet->getRowDimension($row)->setRowHeight($height);
    }

    /**
     * @title 设置单元格背景颜色颜色
     * @param int    $columnIndex 列号
     * @param int    $row         行号
     * @param string $color       颜色（RGB）
     */
    public function setCellColor(int $columnIndex = 1, int $row = 1, string $color = 'cccccc')
    {
        $this->workSheet
            ->getStyleByColumnAndRow($columnIndex, $row)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
    }

    /**
     * @title save
     * @param string $filename
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function save(string $filename = ''): string
    {
        $dir = env('runtime_path') . 'excel' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        is_dir($dir) || mkdir($dir, 0777, true);

        $path = $dir
            . ($filename ? ($filename . '_') : '')
            . date('YmdHis')
            . '_'
            . md5(spl_object_hash($this) . uniqid())
            . '.xlsx';

        $writer = new Xlsx($this->spreadSheet);
        $writer->save($path);
        // 释放内存
        $this->spreadSheet->disconnectWorksheets();
        unset($this->spreadSheet);

        return $path;
    }

    /**
     * @title 提供下载
     * @param string $filename 文件名
     */
    public function download(string $filename = '')
    {
        $filename && $filename .= '_';
        $filename = $filename . date('YmdHis') . '.xlsx';

        ob_end_clean();
        ob_start();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        try {
            $writer = new Xlsx($this->spreadSheet);
            $writer->save('php://output');
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            echo $e->getMessage();
        }

        // 释放内存
        $this->spreadSheet->disconnectWorksheets();
        unset($this->spreadSheet);
        ob_end_flush();

        exit;
    }

    /**
     * @title 优化缓存（解决内存溢出）
     */
    public static function setCache()
    {
        $adapter = new FilesystemAdapter('excel', 3600, env('runtime_path') . '/cache');

        $cache = new Psr16Cache($adapter);

        Settings::setCache($cache);
    }
}
