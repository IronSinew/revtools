<?php

namespace App\Attributes\Enum;

use App\Enums\Attributes\EnumAttributeCommonKeys;
use Attribute;

/** @codeCoverageIgnore */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE)]
final readonly class GenericEnumCaseAttribute
{
    public function __construct(
        public string|EnumAttributeCommonKeys $objectKey,
        public string|int|float|bool $value,
    ) {}
}
