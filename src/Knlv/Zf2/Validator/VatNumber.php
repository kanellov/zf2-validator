<?php

namespace Knlv\Zf2\Validator;

use Ddeboer\Vatin\Exception\ViesException;
use Ddeboer\Vatin\Validator;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception\InvalidArgumentException;
use Zend\Validator\Exception\RuntimeException;

class VatNumber extends AbstractValidator
{
    const INVALID       = 'vatInvalid';
    const FAILED_EXISTS = 'vatFailed';

    protected $messageTemplates = array(
        self::INVALID       => "The VAT is invalid",
        self::FAILED_EXISTS => "Failed to validated existence of VAT",
    );
    /**
     * @var Validator
     */
    protected $validator;

    protected $country;

    protected $checkExistence = true;

    /**
     * Gets the value of validator.
     *
     * @return Validator
     */
    public function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new Validator();
        }

        return $this->validator;
    }

    /**
     * Sets the value of validator.
     *
     * @param Validator $validator the validator
     *
     * @return self
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Gets the value of country.
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the value of country.
     *
     * @param mixed $country the country
     *
     * @return self
     */
    public function setCountry($country)
    {
        if (!$this->getValidator()->isValidCountryCode($country)) {
            throw new InvalidArgumentException('Unsupported country');
        }
        $this->country = $country;

        return $this;
    }

    /**
     * Gets the value of checkExistence.
     *
     * @return mixed
     */
    public function getCheckExistence()
    {
        return $this->checkExistence;
    }

    /**
     * Sets the value of checkExistence.
     *
     * @param mixed $checkExistence the check existence
     *
     * @return self
     */
    public function setCheckExistence($checkExistence)
    {
        $this->checkExistence = (bool) $checkExistence;

        return $this;
    }


    public function isValid($value)
    {
        $this->setValue($value);
        $country = substr($value, 0, 2);
        if ($this->getValidator()->isValidCountryCode($country)) {
            $this->setCountry($country);
            $value = substr($value, 2);
        }
        if (null === $this->getCountry()) {
            throw new RuntimeException('Country not defined');
        }

        try {
            if (!$this->getValidator()->isValid($this->getCountry() . $value, $this->getCheckExistence())) {
                $this->error(self::INVALID);

                return false;
            }
        } catch (ViesException $e) {
            $this->error(self::FAILED_EXISTS);

            return false;
        }

        return true;
    }
}
