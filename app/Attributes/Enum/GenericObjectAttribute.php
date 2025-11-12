<?php

namespace App\Attributes\Enum;

use App\Enums\Attributes\EnumAttributeType;
use Attribute;

/** @codeCoverageIgnore */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class GenericObjectAttribute
{
    public function __construct(
        public string $objectKey,
        public string|int|float|bool|null $value,
        public EnumAttributeType $type,
    ) {}
}
