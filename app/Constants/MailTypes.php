<?php

namespace App\Constants;

/**
 * 通用邮件码常量
 * @Auther Qasim
 * @date 2023/7/16
 */
class MailTypes
{
    //忘记密码
    const RESET_PASSWORD = 1;
    //注册
    const CHANGE_PASSWORD = 2;
    //绑定邮箱
    const BIND_EMAIL = 3;

    //集合
    public static array $types = [
        self::RESET_PASSWORD => 'reset_password',
        self::CHANGE_PASSWORD => 'change_password',
        self::BIND_EMAIL => 'bind_email',
    ];

    public static function getType(int $type): string
    {
        return message(self::$types[$type] ?? '');
    }
}
