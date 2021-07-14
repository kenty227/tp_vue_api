<?php

namespace app\admin\utils;

use ZipArchive;
use app\common\exception\FileException;

class ZipExport
{
    /**
     * @title 打包文件
     * @param array $filePaths 打包文件路径数组
     * @return string
     */
    public static function packageFile(array $filePaths): string
    {
        // 保存目录
        $dir = env('runtime_path') . 'zip' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        is_dir($dir) || mkdir($dir, 0777, true);

        // 文件名
        $fileName = md5(time() . uniqid());
        // 文件完整路径
        $zipFilePath = $dir . $fileName . '.zip';

        // 打包
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
            foreach ($filePaths as $path) {
                file_exists($path) && $zip->addFile($path, basename($path));
            }
            $zip->close();
        }

        if (!file_exists($zipFilePath)) {
            throw new FileException('打包失败');
        }

        // 删除原文件
        foreach ($filePaths as $path) {
            file_exists($path) && unlink($path);
        }

        return $zipFilePath;
    }
}
