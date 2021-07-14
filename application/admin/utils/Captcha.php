<?php
/**
 * @title   自定义验证码类，继承TP验证码类
 * @require topthink/think-captcha=2.0.*
 * @author  WuBaijian
 * @date    2020-09-24
 * @version 1.1
 */

namespace app\admin\utils;

use Firebase\JWT\JWT;
use think\facade\Cache;
use think\facade\Session;
use think\captcha\Captcha as ParentCaptcha;
use app\common\exception\CaptchaException;

/**
 * Class Captcha
 * @package app\admin\utils
 */
class Captcha extends ParentCaptcha
{
    /**
     * @var string 继承父类文件所在目录
     */
    private $_dir;
    /**
     * @var array 配置参数
     */
    private $_config = [
        // 缓存方式 Session, Cache, Token
        'cacheType' => 'Cache',
        // 验证码加密密钥
        'seKey' => 'TP_BASE_CAPTCHA_' . '08f380e4312b16bb794bc3ea3f6cd665',
        // 验证码过期时间（s）
        'expire' => 300,
        // 验证码字体大小(px)
        'fontSize' => 20,
        // 验证码图片高度
        'imageH' => 46,
        // 验证码图片宽度
        'imageW' => 140,
        // 验证码图片宽度
        'length' => 4
    ];
    /**
     * @var resource 验证码图片实例
     */
    private $_im;
    /**
     * @var int 验证码字体颜色
     */
    private $_color;

    /**
     * Captcha constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct(array_merge($this->_config, $config));
        // 获取继承父类文件所在目录
        $this->_dir = dirname((new \ReflectionClass(parent::class))->getFileName());
    }

    /**
     * @title 验证验证码是否正确
     * @param string $code 用户验证码
     * @param string $id   验证码标识 / token
     * @return bool
     */
    public function check($code, $id = ''): bool
    {
        if (strtoupper($this->cacheType) == 'TOKEN') {
            $key = $id; // 缓存方式为token则将token传到id
        } else {
            $key = $this->authcode($this->seKey) . self::getCodeKeyId($id);
        }

        // 验证码不能为空
        $secode = $this->getCodeCache($key);
        if (empty($code) || empty($secode)) {
            throw new CaptchaException('验证码不能为空');
        }
        // 过期
        if (time() - $secode['verify_time'] > $this->expire) {
            $this->deleteCodeCache($key);
            throw new CaptchaException('验证码已过期');
        }

        if ($this->authcode(strtoupper($code)) == $secode['verify_code']) {
            $this->deleteCodeCache($key);
            return true;
        }

        throw new CaptchaException('验证码错误');
    }

    /**
     * @title 输出验证码并保存验证码的值
     *        格式为：['verify_code' => '验证码值', 'verify_time' => '验证码创建时间']
     * @param string $id 要生成验证码的标识
     * @return \think\Response
     */
    public function entry($id = ''): \think\Response
    {
        // 图片宽(px)
        $this->imageW || $this->imageW = $this->length * $this->fontSize * 1.5 + $this->length * $this->fontSize / 2;
        // 图片高(px)
        $this->imageH || $this->imageH = $this->fontSize * 2.5;
        // 建立一幅 $this->imageW x $this->imageH 的图像
        $this->_im = imagecreate($this->imageW, $this->imageH);
        // 设置背景
        imagecolorallocate($this->_im, $this->bg[0], $this->bg[1], $this->bg[2]);

        // 验证码字体随机颜色
        $this->_color = imagecolorallocate($this->_im, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
        // 验证码使用随机字体
        $ttfPath = $this->_dir . '/../assets/' . ($this->useZh ? 'zhttfs' : 'ttfs') . '/';

        if (empty($this->fontttf)) {
            $dir = dir($ttfPath);
            $ttfs = [];
            while (false !== ($file = $dir->read())) {
                if ('.' != $file[0] && substr($file, -4) == '.ttf') {
                    $ttfs[] = $file;
                }
            }
            $dir->close();
            $this->fontttf = $ttfs[array_rand($ttfs)];
        }
        $this->fontttf = $ttfPath . $this->fontttf;

        if ($this->useImgBg) {
            $this->background();
        }
        if ($this->useNoise) {
            // 绘杂点
            $this->writeNoise();
        }
        if ($this->useCurve) {
            // 绘干扰线
            $this->writeCurve();
        }

        // 绘验证码
        $code = []; // 验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        if ($this->useZh) {
            // 中文验证码
            for ($i = 0; $i < $this->length; $i++) {
                $code[$i] = iconv_substr($this->zhSet, floor(mt_rand(0, mb_strlen($this->zhSet, 'utf-8') - 1)), 1, 'utf-8');
                imagettftext($this->_im, $this->fontSize, mt_rand(-40, 40), $this->fontSize * ($i + 1) * 1.5, $this->fontSize + mt_rand(10, 20), $this->_color, $this->fontttf, $code[$i]);
            }
        } else {
            for ($i = 0; $i < $this->length; $i++) {
                $code[$i] = $this->codeSet[mt_rand(0, strlen($this->codeSet) - 1)];
                $codeNX += mt_rand($this->fontSize * 1.2, $this->fontSize * 1.6);
                imagettftext($this->_im, $this->fontSize, mt_rand(-40, 40), $codeNX, $this->fontSize * 1.6, $this->_color, $this->fontttf, $code[$i]);
            }
        }

        $headers = [];

        // 保存验证码
        $key = $this->authcode($this->seKey);
        $code = $this->authcode(strtoupper(implode('', $code)));
        $secode = [
            'verify_code' => $code, // 校验码
            'verify_time' => time() // 验证码创建时间
        ];

        $result = $this->setCodeCache($key . self::getCodeKeyId($id), $secode);
        $result && $headers['token'] = $result;

        ob_start();
        // 输出图像
        imagepng($this->_im);
        $content = ob_get_clean();
        imagedestroy($this->_im);

        $headers['Content-Length'] = strlen($content);
        return response($content, 200, $headers)->contentType('image/png');
    }

    /**
     * @title 获取验证码标识
     * @param string $id 原验证码标识
     * @return string
     */
    private static function getCodeKeyId(string $id): string
    {
        return $id ?: (string)request()->ip(1);
    }

    /**
     * @title 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数)
     * 正弦型函数解析式：y=Asin(ωx+φ)+b
     * 各常数值对函数图像的影响：
     *     A：决定峰值（即纵向拉伸压缩的倍数）
     *     b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *     φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *     ω：决定周期（最小正周期T=2π/∣ω∣）
     */
    private function writeCurve()
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, $this->imageH / 2); // 振幅
        $b = mt_rand(-$this->imageH / 4, $this->imageH / 4); // Y轴方向偏移量
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4); // X轴方向偏移量
        $T = mt_rand($this->imageH, $this->imageW * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand($this->imageW / 2, $this->imageW * 0.8); // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2; // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->_im, $px + $i, $py + $i, $this->_color); // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A = mt_rand(1, $this->imageH / 2); // 振幅
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4); // X轴方向偏移量
        $T = mt_rand($this->imageH, $this->imageW * 2); // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->imageH / 2;
        $px1 = $px2;
        $px2 = $this->imageW;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2; // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->_im, $px + $i, $py + $i, $this->_color);
                    $i--;
                }
            }
        }
    }

    /**
     * @title 画杂点（往图片上写不同颜色的字母或数字）
     */
    private function writeNoise()
    {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for ($i = 0; $i < 10; $i++) {
            //杂点颜色
            $noiseColor = imagecolorallocate($this->_im, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->_im, 5, mt_rand(-10, $this->imageW), mt_rand(-10, $this->imageH), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }

    /**
     * @title 绘制背景图片（注：如果验证码输出图片比较大，将占用比较多的系统资源）
     */
    private function background()
    {
        $path = $this->_dir . '/../assets/bgs/';
        $dir = dir($path);

        $bgs = [];
        while (false !== ($file = $dir->read())) {
            if ('.' != $file[0] && substr($file, -4) == '.jpg') {
                $bgs[] = $path . $file;
            }
        }
        $dir->close();

        $gb = $bgs[array_rand($bgs)];

        list($width, $height) = @getimagesize($gb);
        // Resample
        $bgImage = @imagecreatefromjpeg($gb);
        @imagecopyresampled($this->_im, $bgImage, 0, 0, 0, 0, $this->imageW, $this->imageH, $width, $height);
        @imagedestroy($bgImage);
    }

    /**
     * @title 加密验证码
     * @param string $str
     * @return string
     */
    private function authcode(string $str): string
    {
        $key = substr(md5($this->seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }

    /**
     * @title setCodeCache
     * @param string $key
     * @param array  $secode
     * @return string|void
     */
    private function setCodeCache(string $key, array $secode)
    {
        switch (strtoupper($this->cacheType)) {
            case 'SESSION':
                Session::set($key, $secode, '');
                return;
            case 'CACHE':
                Cache::set($key, $secode, $this->expire);
                return;
            case 'TOKEN':
                $secode['exp'] = $secode['verify_time'] + $this->expire;
                return JWT::encode($secode, $this->seKey, 'HS256');
            default:
                return;
        }
    }

    /**
     * @title getCodeCache
     * @param string $key 缓存key / token值
     * @return array|mixed|null
     */
    private function getCodeCache(string $key)
    {
        switch (strtoupper($this->cacheType)) {
            case 'SESSION':
                return Session::get($key, '');
            case 'CACHE':
                return Cache::get($key);
            case 'TOKEN':
                try {
                    $payload = JWT::decode($key, $this->seKey, ['HS256']);
                    return (array)$payload;
                } catch (\Exception $e) {
                    return null;
                }
            default:
                return null;
        }
    }

    /**
     * @title deleteCodeCache
     * @param string $key
     */
    private function deleteCodeCache(string $key)
    {
        switch (strtoupper($this->cacheType)) {
            case 'SESSION':
                Session::delete($key, '');
                break;
            case 'CACHE':
                Cache::rm($key);
                break;
            case 'TOKEN':
                // TODO 销毁token（待完善）
                break;
            default:
                break;
        }
    }
}
