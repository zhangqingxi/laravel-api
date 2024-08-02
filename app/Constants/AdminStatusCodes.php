<?php

namespace App\Constants;

/**
 * 接口状态码常量
 * @Auther Qasim
 * @date 2023/6/28
 */
class AdminStatusCodes
{
    // 成功
    const SUCCESS = 1000;

    //User
    const ACCOUNT_LOCKED = 1100;
    const UNAUTHORIZED = 1101;
    const EMAIL_NOT_EXIST = 1102;
    const EMAIL_EXIST = 1103;
    const EMAIL_TYPE_NOT_ALLOWED = 1104;
    const EMAIL_INVALID_CODE = 1105;

    const EMAIL_CODE_EXCEEDED = 1106;

    const PHONE_EXIST = 1107;

    const USER_NOT_ALLOWED = 1108;


    const USER_LOGIN_OTHER_DEVICE = 1102;

    const USER_MENUS_NOT_FOUND = 1200;
    const USER_PASSWORD_INVALID = 1201;

    //File
    const FILE_NOT_FOUND = 1300;
    const FILE_UPLOAD_FAILED = 1301;
    const FILE_DELETE_FAILED = 1302;
    const FILE_TYPE_NOT_ALLOWED = 1303;

    //Menu
    const MENU_HAS_CHILDREN = 1400;

    //Role
    const ROLE_EXIST = 1500;
    const ROLE_NOT_ALLOWED = 1501;

    //File
    const FILE_ASSOCIATION_NOT_FOUND = 1604;
    const FILE_ASSOCIATION_EXIST = 1605;
    const FILE_HAS_ASSOCIATION = 1606;
}
