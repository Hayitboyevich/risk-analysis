<?php

namespace App\Enums;

enum UserRoleEnum: int
{
    case KVARTIRA_INSPECTOR = 31;
    case GASN_INSPECTOR = 32;
    case SUV_INSPECTOR = 33;
    case EKOLOGIYA = 34;
    case EKOLOGIYA_RES_KADR = 35;
    case HTQ_KADR = 37;
}
