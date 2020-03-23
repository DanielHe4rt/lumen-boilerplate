<?php


namespace App\Enums\User;


use App\Enums\Enum;

abstract class GenderTypes extends Enum
{
    const MALE = 1;
    const FEMALE = 2;
    const BINARY = 3;
    const NONBINARY = 4;
    const NONE = 5;
    const LIST = [
        self::MALE => 'Masculino',
        self::FEMALE => 'Feminino',
        self::BINARY => 'Binário',
        self::NONBINARY => 'Não Binário',
        self::NONE => 'Outro'
    ];
}
