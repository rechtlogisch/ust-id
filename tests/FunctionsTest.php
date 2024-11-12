<?php

use Rechtlogisch\UstId\Dto\ValidationResult;

it('validates an ust-id with the global validateUstId() function', function (string $ustId) {
    $result = validateUstId($ustId);

    expect($result)->toBeInstanceOf(ValidationResult::class)
        ->and($result->isValid())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
})->with('valid');

it('validates an ust-id with the global isValidUstId() function', function (string $ustId) {
    $result = isUstIdValid($ustId);

    expect($result)->toBeTrue();
})->with('valid');
