<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/5 22:49
 */

namespace App\Services;

use App\Exceptions\CaptchaVerifyException;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\Str;

class ImageCaptchaService
{
    const TYPE_REGISTER_PHONE = "register_phone";

    /**
     * 创建图形验证码
     *
     * @param string $type        业务类型
     * @param int    $secondTTL   有效时间(秒)
     * @param int    $verifyLimit 最多可校验次数(防暴力破解), -1 表示不限制校验次数
     * @param array  $ext         额外数据(校验时需使用)
     *
     * @return array
     */
    public function createApi($type, $secondTTL, $verifyLimit = 1, $ext = [])
    {
        // $builder = (new PhraseBuilder())->build();
        $builder = new CaptchaBuilder();
        $captcha = $builder->build();

        $code = $captcha->getPhrase();

        $key = $this->generateKey();
        $expire = now()->addSeconds($secondTTL);
        \Cache::put(
            $key,
            [
                'code' => $code,                // 验证码
                'type' => $type,                // 业务类型
                'available' => $verifyLimit,    // 剩余可校验次数
                'expire' => $expire,            // 过期时间
                'ext' => $ext,                  // 额外数据(校验用)
            ],
            $expire
        );

        return [
            'code' => $code,
            'base64' => $captcha->inline(),
            'key' => $key,
            'expire' => $expire,
        ];
    }

    /**
     * 校验图形验证码
     *
     * @param string $type
     * @param string $key
     * @param string $code
     * @param array  $ext 若传入额外数据, 则会一并校验 key 对应的 value 是否一致
     *
     * @throws CaptchaVerifyException
     */
    public function verifyApi($type, $key, $code, $ext = [])
    {
        $cached = \Cache::get($key);
        if (empty($cached)) {
            throw new CaptchaVerifyException(CaptchaVerifyException::ERR_INVALID, "校验码已失效");
        }

        // 验证码校验失败
        if (!PhraseBuilder::comparePhrases($cached['code'], $code)) {
            $this->afterVerifyApi($key, $cached, false);
            throw new CaptchaVerifyException(CaptchaVerifyException::ERR_CODE_NO_MATCH, "校验码错误");
        }

        // 业务类型校验失败
        if ($cached['type'] != $type) {
            $this->afterVerifyApi($key, $cached, false);
            throw new CaptchaVerifyException(CaptchaVerifyException::ERR_TYPE_NO_MATCH, "业务类型校验错误", ['raw' => $cached['type'], 'new' => $type]);
        }

        // 额外数据校验
        if (!empty($ext)) {
            foreach ($ext as $k => $v) {
                if ($cached['ext'][$k] != $v) {
                    $this->afterVerifyApi($key, $cached, false);
                    throw new CaptchaVerifyException(CaptchaVerifyException::ERR_EXT_NO_MATCH, "额外数据校验失败", ['raw' => $cached['ext'], 'new' => $ext]);
                }
            }
        }

        $this->afterVerifyApi($key, $cached, true);
    }

    protected function afterVerifyApi(string $key, array $cached, bool $success)
    {
        if ($success) {
            \Cache::forget($key);
            return;
        }

        $cached['available']--;

        // 还有可校验次数
        if ($cached['available'] > 0) {
            \Cache::put($key, $cached, $cached['expire']);
        } else {
            \Cache::forget($key);
        }
    }

    /**
     * 生成随机 key
     * @return string
     */
    protected function generateKey()
    {
        return "captcha:image:" . Str::random(16);
    }
}