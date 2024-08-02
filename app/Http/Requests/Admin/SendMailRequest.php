<?php

namespace App\Http\Requests\Admin;

use App\Constants\MailTypes;

/**
 * @property string $email 邮箱
 * @property int $type 邮件类型
 */
class SendMailRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'type' => 'required|in:' . implode(',', array_keys(MailTypes::$types)),
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => message('email_type'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
