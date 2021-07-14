<?php

namespace app\common\utils;

use think\File;
use app\common\exception\FileException;

class Upload
{
    /**
     * @var string|null
     */
    private static $domain = null;
    /**
     * @var array 允许上传资源后缀
     */
    public static $ext = [
        'txt',
        'doc', 'docx',
        'xls', 'xlsx',
        'mp3', 'mp4',
        'jpg', 'png', 'gif'
    ];

    /**
     * @title getDomain
     * @return string
     */
    public static function getDomain(): string
    {
        if (is_null(self::$domain)) {
            self::$domain = env('upload.domain', '');
        }
        return self::$domain;
    }

    /**
     * @title 获取上传文件持久化路径
     * @param string $url
     * @return string
     */
    public static function getSavePath(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        return str_replace(self::getDomain(), '', $url);
    }

    /**
     * @title 获取上传文件访问URL
     * @param string $url
     * @return string
     */
    public static function getAccessUrl(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $domain = self::getDomain();
        if (strpos($url, $domain) === false && strpos($url, 'http') !== 0) {
            $url = $domain . $url;
        }

        return $url;
    }

    /**
     * @title 是否上传到OSS
     * @return bool
     */
    public static function isUploadOss(): bool
    {
        $isUploadOss = env('upload.oss', false);
        if ($isUploadOss === true || $isUploadOss === 'true' || $isUploadOss === '1') {
            return true;
        }
        return false;
    }

    /**
     * @title uploadFile
     * @param File   $file 上传文件
     * @param string $dir  上传目录
     * @return array
     * @throws FileException
     */
    public static function uploadFile(File $file, string $dir = 'common'): array
    {
        if (!self::isUploadOss()) {
            return self::uploadFile2Local($file, $dir);
        } else {
            return self::uploadFile2Oss($file, $dir);
        }
    }

    /**
     * @title uploadImage
     * @param string $image
     * @param string $ext
     * @param string $dir
     * @return array
     * @throws FileException
     */
    public static function uploadImage(string $image, string $ext = 'jpg', string $dir = 'image'): array
    {
        if (!self::isUploadOss()) {
            return self::uploadImage2Local($image, $ext, $dir);
        } else {
            return self::uploadImage2Oss($image, $ext, $dir);
        }
    }

    /**
     * @title uploadBase64Image
     * @param string $base64Image 上传图片
     * @param string $dir         上传目录
     * @return array
     * @throws FileException
     */
    public static function uploadBase64Image(string $base64Image, string $dir = 'image'): array
    {
        // 获取二进制格式图片
        if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Image, $result)) {
            self::exception('上传图片格式错误');
        }
        // 允许图片格式：JPG、PNG、GIF
        $ext = strtolower($result[2]);
        if (empty($result) || count($result) < 3 || !in_array($ext, ['jpg', 'png', 'gif'])) {
            self::exception('上传图片格式错误');
        }

        $imageString = base64_decode(str_replace($result[1], '', $base64Image));

        if (!self::isUploadOss()) {
            return self::uploadImage2Local($imageString, $ext, $dir);
        } else {
            return self::uploadImage2Oss($imageString, $ext, $dir);
        }
    }

    /**
     * @title uploadFile2Local
     * @param File   $file
     * @param string $dir
     * @return array
     * @throws FileException
     */
    private static function uploadFile2Local(File $file, string $dir): array
    {
        $relativePath = 'uploads' . '/' . $dir . '/';

        $path = self::checkDirReturnPath(self::getRootPath() . $relativePath);

        $info = $file->validate(['ext' => self::$ext])->move($path);
        if (!$info) {
            self::exception('上传失败');
        }

        $saveName = strpos(PHP_OS, 'WIN') !== false
            ? str_replace('\\', '/', $info->getSaveName()) // 转换win系统分隔符
            : $info->getSaveName();
        return [
            'name' => $file->getInfo('name'),
            'path' => $relativePath . $saveName,
            'url' => self::getDomain() . $relativePath . $saveName
        ];
    }

    /**
     * @title uploadImage2Local
     * @param string $image
     * @param string $ext
     * @param string $dir
     * @return array
     * @throws FileException
     */
    private static function uploadImage2Local(string $image, string $ext, string $dir): array
    {
        $relativePath = 'uploads' . '/' . $dir . '/'
            . date('Ymd') . '/';

        $path = self::checkDirReturnPath(self::getRootPath() . $relativePath);

        $name = self::autoBuildName($ext);

        try {
            $fp = fopen($path . $name, 'w+');
            fwrite($fp, $image);
            fclose($fp);
        } catch (\Exception $e) {
            self::exception('上传失败');
        }

        return [
            'name' => $name,
            'path' => $relativePath . $name,
            'url' => self::getDomain() . $relativePath . $name
        ];
    }

    /**
     * @title uploadFile2Oss
     * @param File   $file
     * @param string $dir
     * @return array
     */
    private static function uploadFile2Oss(File $file, string $dir): array
    {
        // TODO ...
        $path = '';

        return [
            'name' => $file->getInfo('name'),
            'path' => $path,
            'url' => self::getDomain() . $path
        ];
    }

    /**
     * @title uploadImage2Oss
     * @param string $image
     * @param string $ext
     * @param string $dir
     * @return array
     */
    private static function uploadImage2Oss(string $image, string $ext, string $dir): array
    {
        // TODO ...
        $path = '';

        return [
            'name' => substr($path, strrpos($path, '/') + 1),
            'path' => $path,
            'url' => self::getDomain() . $path
        ];
    }

    /**
     * @title checkDirReturnPath
     * @param string $path
     * @return string
     */
    private static function checkDirReturnPath(string $path): string
    {
        is_dir($path) || mkdir($path, 0777, true);
        return $path;
    }

    /**
     * @title autoBuildName
     * @param string $ext
     * @return string
     */
    private static function autoBuildName(string $ext = ''): string
    {
        $name = md5(microtime(true));
        return $ext ? ($name . ".{$ext}") : $name;
    }

    /**
     * @title getRootPath
     * @return string
     */
    private static function getRootPath(): string
    {
        return env('root_path') . 'public' . '/';
    }

    /**
     * @title exception
     * @param string $message
     * @throws FileException
     */
    private static function exception(string $message)
    {
        throw new FileException($message);
    }
}
