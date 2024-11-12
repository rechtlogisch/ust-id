<?php

use Rechtlogisch\UstId\Dto\ValidationResult;
use Rechtlogisch\UstId\Exceptions;
use Rechtlogisch\UstId\UstId;

it('returns a ValidationResult on valid input', function (string $ustId) {
    $result = (new UstId($ustId))->validate();
    expect($result)->toBeObject(ValidationResult::class);
})->with('valid');

it('returns a ValidationResult on invalid input', function (string $ustId) {
    $result = (new UstId($ustId))->validate();
    expect($result)->toBeObject(ValidationResult::class);
})->with('invalid');

it('returns true for a valid ust-id', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
})->with('valid');

it('returns false for a valid ust-id with spaces', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->not->toBeEmpty();
})->with('valid-but-with-spaces-therefore-invalid');

it('returns false for an invalid ust-id', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->not->toBeEmpty();
})->with('invalid');

it('throws a type error when nothing provided as ust-id', function () {
    /** @noinspection PhpParamsInspection */
    new UstId; /** @phpstan-ignore-line */
})->throws(TypeError::class);

it('throws a type error when null provided as ust-id', function () {
    new UstId(null); /** @phpstan-ignore-line */
})->throws(TypeError::class);

it('returns false when ust-id does not start with DE', function (string $input) {
    $result = (new UstId($input))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidUstIdPrefix::class)
        ->and($result->getFirstError())->toContain('USt-ID must start with "DE". You provided: '.mb_substr($input, 0, 2));
})->with([
    'AA',
    '123',
]);

it('returns false and a specific error message when ust-id does not start with uppercase DE', function (string $input) {
    $result = (new UstId($input))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\UstIdPrefixMustBeUppercase::class);
})->with([
    'de',
    'De',
    'dE',
]);

it('returns false and specific error message when empty string provided as input', function () {
    $result = (new UstId(''))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InputEmpty::class);
});

it('returns false and specific error message when input to short', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toContain(Exceptions\InvalidUstIdLength::class);
})->with([
    'DE1',
    'DE12',
    'DE123',
    'DE1234',
    'DE12345',
    'DE123456',
    'DE1234567',
    'DE12345678',
]);

it('returns false and specific error message when input to long', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toContain(Exceptions\InvalidUstIdLength::class);
})->with([
    'DE1234567890',
    'DE12345678901',
]);

it('returns false and specific error message when an ust-id contains non-digits after DE', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\UstIdCanContainOnlyDigitsAfterDe::class);
})->with([
    'DE12345678X',
    'DEx23456789',
    'DE12-456789',
    'DE123.56789',
    'DE1234 6789',
    'DE12345,789',
    'DE123456_89',
]);

it('returns checkDigit `0` for ust-id with check digit `10`', function (string $ustId) {
    $result = (new UstId($ustId))->validate();

    expect($result->isValid())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
})->with([
    'DE123456840',
]);
