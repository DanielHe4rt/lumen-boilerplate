<?php


namespace App\Enums\Auth;


use App\Enums\Enum;

abstract class TokenTypes extends Enum
{
    const EMAIL = 'email';
    const SMS = 'sms';
    const PUSH_NOTIFICATION = 'push';
}
