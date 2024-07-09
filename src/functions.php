<?php

declare(strict_types=1);

use Rechtlogisch\UstId\Dto\ValidationResult;
use Rechtlogisch\UstId\UstId;

function validateUstId(string $input): ValidationResult
{
    return (new UstId($input))->validate();
}

function isUstIdValid(string $input): ?bool
{
    return (new UstId($input))->validate()->isValid();
}
