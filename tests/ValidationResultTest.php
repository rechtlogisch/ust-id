<?php

use Rechtlogisch\UstId\Dto\ValidationResult;

it('returns null as first error when no errors set', function () {
    $dto = new ValidationResult;
    $firstError = $dto->getFirstError();

    expect($firstError)->toBeNull();
});
