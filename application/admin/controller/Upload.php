<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\common\utils\Upload as UploadUtil;

class Upload extends CommonController
{
    /**
     * @title 通用文件上传
     * @param string $dir
     * @return array
     * @throws \app\common\exception\FileException
     */
    public function index(string $dir = 'common'): array
    {
        $file = $this->request->file('file');
        if (!$file) {
            return $this->returnError('请上传文件');
        }

        if (!$file->validate(['ext' => UploadUtil::$ext])) {
            return $this->returnError('上传文件类型错误');
        }

        // 判断单文件上传还是多文件上传
        if (is_array($file)) {
            foreach ($file as $key => $value) {
                $data[$key] = UploadUtil::uploadFile($value, $dir);
            }
        } else {
            $data = [UploadUtil::uploadFile($file, $dir)];
        }

        return $this->returnSuccess('上传成功', $data);
    }
}
