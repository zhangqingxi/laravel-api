<?php

namespace App\Http\Requests\Admin;

use App\Constants\MailTypes;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $email 邮箱
 * @property int $code 验证码
 * @property int $type 邮件类型
 */
class VerifyMailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'code' => 'required|size:6',
            'type' => 'required|in:' . implode(',', array_keys(MailTypes::$types)),
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => message('email_type', 'admin'),
            'code' => message('email_code', 'admin'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
