<?php

namespace App\Enum;

enum QrStatusEnum: string
{
    case ACTIVE = 'ACTIVE';
    case ARCHIVED = 'ARCHIVED';

    /**
     * @return array<string,string>
     */
    public static function getAsArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, QrStatusEnum $type) => $choices + [$type->name => $type->value],
            [],
        );
    }
}
