<?php

/**
 * Knlv\Zf2\Validator\ExplodeWithContext
 *
 * @link https://github.com/kanellov/zf2-validator
 * @copyright Copyright (c) 2015 Vassilis Kanellopoulos - contact@kanellov.com
 * @license https://raw.githubusercontent.com/kanellov/zf2-validator/master/LICENSE
 */

namespace Knlv\Zf2\Validator;

use Zend\Validator\Explode;
use Zend\Validator\Exception\RuntimeException;

class ExplodeWithContext extends Explode
{
    /**
     * Defined by Zend\Validator\ValidatorInterface
     *
     * Returns true if all values validate true
     *
     * @param  mixed $value
     * @param  mixed $context Additional context
     * @return bool
     * @throws RuntimeException
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if ($value instanceof Traversable) {
            $value = ArrayUtils::iteratorToArray($value);
        }

        if (is_array($value)) {
            $values = $value;
        } elseif (is_string($value)) {
            $delimiter = $this->getValueDelimiter();
            // Skip explode if delimiter is null,
            // used when value is expected to be either an
            // array when multiple values and a string for
            // single values (ie. MultiCheckbox form behavior)
            $values = (null !== $delimiter)
                      ? explode($this->valueDelimiter, $value)
                      : array($value);
        } else {
            $values = array($value);
        }

        $validator = $this->getValidator();

        if (!$validator) {
            throw new RuntimeException(sprintf(
                '%s expects a validator to be set; none given',
                __METHOD__
            ));
        }

        foreach ($values as $value) {
            // provide context to validators isValid method
            if (!$validator->isValid($value, $context)) {
                $this->abstractOptions['messages'][] = $validator->getMessages();

                if ($this->isBreakOnFirstFailure()) {
                    return false;
                }
            }
        }

        return count($this->abstractOptions['messages']) == 0;
    }
}
