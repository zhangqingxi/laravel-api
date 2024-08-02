<?php

namespace App\Mail;

use App\Constants\MailTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $code;
    protected string $type;

    public function __construct(string $code, int $type)
    {
        $this->code = $code;
        $this->type = MailTypes::getType($type);
    }

    public function build(): VerificationCodeMail
    {
        return $this->subject(message('email_verification', 'blade'))
            ->view('emails.verification_code')
            ->with(['code' => $this->code, 'type' => $this->type]);
    }
}
