<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bbs:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->ask("请输入用户 id/email/phone");
        $credential = [];
        if (preg_match(re_phone(true), $key)) {
            $credential['phone'] = $key;
            $by = "手机号";
        } elseif (filter_var($key, FILTER_VALIDATE_EMAIL) !== false) {
            $credential['email'] = $key;
            $by = "邮箱";
        } elseif (preg_match('/^[1-9][0-9]*$/', $key)) {
            $credential['id'] = $key;
            $by = "用户Id";
        } else {
            $this->error("格式错误");
            return;
        }

        $user = User::query()->where($credential)->first();
        if (!$user) {
            $this->error("用户不存在");
            return;
        }

        $token = auth('api')->login($user);
        $this->info("$by($key) token: " . $token);
    }
}
