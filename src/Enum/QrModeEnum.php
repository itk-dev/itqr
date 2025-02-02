<?php

namespace App\Enum;

enum QrModeEnum: string
{
    case DEFAULT = 'default';

    /**
     * @return array<string,string>
     */
    public static function getAsArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, QrModeEnum $type) => $choices + [$type->name => $type->value],
            [],
        );
    }
}
