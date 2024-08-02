<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

/**
 * 发送邮件队列任务
 * @Auther Qasim
 * @date 2023/6/28
 */
class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $code;
    protected int $type;

    public function __construct(string $email, string $code, int $type)
    {
        $this->email = $email;
        $this->code = $code;
        $this->type = $type;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new VerificationCodeMail($this->code, $this->type));
    }
}
