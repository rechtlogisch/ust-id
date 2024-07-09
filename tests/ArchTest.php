<?php

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

test('dtos are final')
    ->expect('Rechtlogisch\UstId\Dto')
    ->toBeFinal();

test('use strict mode')
    ->expect('Rechtlogisch\UstId')
    ->toUseStrictTypes();
