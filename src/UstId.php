<?php

declare(strict_types=1);

namespace Rechtlogisch\UstId;

use Rechtlogisch\UstId\Dto\ValidationResult;
use Throwable;

class UstId
{
    /** @var int */
    private const LENGTH_UST_ID = 11;

    /** @var string */
    private const PREFIX = 'DE';

    private ValidationResult $result;

    public function __construct(
        public string $input
    ) {
        $this->result = new ValidationResult();

        try {
            $this->guard();
        } catch (Throwable $exception) {
            $exceptionType = get_class($exception);
            $this->result->setValid(false);
            $this->result->addError($exceptionType, $exception->getMessage());
        }
    }

    public function validate(): ValidationResult
    {
        if ($this->result->isValid() === false) {
            return $this->result;
        }

        $hasValidChecksum = mb_substr($this->input, -1) === (string) $this->checkDigit();
        $this->result->setValid($hasValidChecksum);

        if ($hasValidChecksum === false) {
            $this->result->addError(Exceptions\InvalidCheckDigit::class, 'Check digit in the provided USt-ID is invalid.');
        }

        return $this->result;
    }

    private function guard(): void
    {
        if (empty($this->input)) {
            throw new Exceptions\InputEmpty('Please provide a non-empty input as USt-ID.');
        }

        if (($prefix = mb_substr($this->input, 0, 2)) !== self::PREFIX) {
            if (ctype_alpha($prefix) && ! ctype_upper($prefix)) {
                throw new Exceptions\UstIdPrefixMustBeUppercase('USt-ID prefix must be uppercase "'.self::PREFIX.'". You provided: '.$prefix);
            }
            throw new Exceptions\InvalidUstIdPrefix('USt-ID must start with "'.self::PREFIX.'". You provided: '.$prefix);
        }

        if (($lengthInput = mb_strlen($this->input)) !== self::LENGTH_UST_ID) {
            throw new Exceptions\InvalidUstIdLength('USt-ID must be '.self::LENGTH_UST_ID.' characters long. Provided USt-ID is: '.$lengthInput.' characters long.');
        }

        if (! ctype_digit(mb_substr($this->input, 2))) {
            throw new Exceptions\UstIdCanContainOnlyDigitsAfterDe('Only digits are allowed after "'.self::PREFIX.'" prefix.');
        }
    }

    public function checkDigit(): int
    {
        $digits = mb_substr($this->input, 2);

        $product = 10;
        for ($i = 0; $i < 8; $i++) {
            $sum = (int) $digits[$i] + $product;
            $sum %= 10;
            if ($sum === 0) {
                $sum = 10;
            }
            $product = (2 * $sum) % 11;
        }

        $checkDigit = 11 - $product;
        if ($checkDigit === 10) {
            $checkDigit = 0;
        }

        return $checkDigit;
    }
}
