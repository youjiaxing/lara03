<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 10:55
 */

namespace App\Services;

use App\Exceptions\CaptchaVerifyException;
use Gregwar\Captcha\PhraseBuilder;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class SmsCaptchaService
{
    protected $sms;


    /**
     * SmsService constructor.
     */
    public function __construct(EasySms $sms)
    {
        $this->sms = $sms;
    }

    /**
     * @param string $phone
     *
     * @return array = [
     *     $code,
     *     $expire,
     * ]
     */
    public function sendRegisterCaptcha($phone, $secondTTL, $verifyLimit = 1)
    {
        if (!preg_match('/^[1-9][0-9]{10}$/', $phone)) {
            throw new \Exception("手机号格式错误: " . $phone);
        }

        $code = $this->generateCode();

        if (!preg_match('/^[0-9a-zA-Z]{1,6}$/', $code)) {
            throw new \Exception("验证码格式错误: " . $code);
        }

        $this->sendSms($phone, "您的验证码${code}, 该验证码5分钟内有效, 请勿泄露于他人!", config('easysms.template.register'), ['code' => $code]);

        // 保存在缓存中
        $key = $this->generateKey($phone);
        $expire = now()->addSeconds($secondTTL);
        \Cache::put(
            $key,
            [
                'code' => $code,                // 验证码
                'available' => $verifyLimit,    // 剩余可校验次数
                'expire' => $expire,            // 过期时间
            ],
            $expire
        );

        return [$code, $expire];
    }

    public function sendSms($phone, $content, $template, $data)
    {
        // if ($this->isFake()) {
        //     return;
        // }

        try {
            $this->sms->send(
                $phone,
                [
                    'content' => $content,
                    'template' => $template,
                    'data' => $data,
                ]
            );
        } catch (InvalidArgumentException $e) {
            $msg = "发送短信失败: 参数错误";
            \Log::warning($msg, $e->getResults());
            throw new \Exception($msg, 0, $e);
        } catch (NoGatewayAvailableException $e) {
            $msg = "发送短信失败: 网关无效";
            \Log::warning($msg, $e->getResults());
            throw new \Exception($msg, 0, $e);
        } catch (\Throwable $e) {
            $msg = "发送短信失败: 未知";
            \Log::warning($msg, $e->getResults());
            throw new \Exception($msg, 0, $e);
        }
        \Log::info("发送验证码短信 -> ${phone}");
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
    public function verifyApi($phone, $code)
    {
        $key = $this->generateKey($phone);
        $cached = \Cache::get($key);
        if (empty($cached)) {
            throw new CaptchaVerifyException(CaptchaVerifyException::ERR_INVALID, "校验码已失效");
        }

        // 验证码校验失败
        if (!PhraseBuilder::comparePhrases($cached['code'], $code)) {
            $this->afterVerifyApi($key, $cached, false);
            throw new CaptchaVerifyException(CaptchaVerifyException::ERR_CODE_NO_MATCH, "校验码错误");
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
     * 生成短信用的验证码
     * @return string
     */
    protected function generateCode()
    {
        $code = mt_rand(1, 9999);
        return str_pad($code, 4, "0", STR_PAD_LEFT);
    }

    protected function generateKey($phone)
    {
        return "captcha:sms:" . $phone;
    }
}