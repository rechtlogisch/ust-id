<?php

use Rechtlogisch\UstId\UstId;

it('generates an ust-id with check digit `0`', function () {
    $class = new ReflectionClass(UstId::class);
    try {
        $method = $class->getMethod('checkDigit');
    } catch (ReflectionException $e) {
        exit($e->getMessage());
    }
    /** @noinspection PhpExpressionResultUnusedInspection */
    $method->setAccessible(true);

    $ustIdPrefix = 'DE';
    $ustIdDigits = '123456780';

    do {
        $ustId = $ustIdPrefix.$ustIdDigits;
        $object = new UstId($ustId);

        try {
            $checkDigit = $method->invokeArgs($object, []);
        } catch (ReflectionException $e) {
            exit($e->getMessage());
        }

        if ($checkDigit !== 0) {
            $nextUstIdDigits = (int) $ustIdDigits + 10;
            $ustIdDigits = (string) $nextUstIdDigits;

            continue;
        }

        if ((new UstId($ustId))->validate()->isValid() !== true) {
            $nextUstIdDigits = (int) $ustIdDigits + 10;
            $ustIdDigits = (string) $nextUstIdDigits;

            continue;
        }

        break;
    } while (true);

    //    echo $ustId;

    $lastDigit = (int) $ustIdDigits % 10;
    expect($lastDigit)->toBe(0);
});
