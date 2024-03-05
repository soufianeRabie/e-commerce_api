<?php

namespace App\Enums;

enum  UserRoleEnum :int
{
    case CLIENT = 0;
    case EMPLOYEE = 1;
    case PARTNER = 2;
    case ADMIN = 4;
}
