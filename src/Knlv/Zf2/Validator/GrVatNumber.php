<?php

namespace Knlv\Zf2\Validator;

use Knlv\greek_vat_validator;
use Zend\Validator\AbstractValidator;

class GrVatNumber extends AbstractValidator
{
    const INVALID       = 'vatInvalid';

    protected $messageTemplates = array(
            self::INVALID       => "The VAT is invalid",
    );

    public function isValid($value)
    {
        $this->setValue($value);
        if (!greek_vat_validator($value)) {
            $this->error(self::INVALID);

            return false;
        }

        return true;
    }
}
