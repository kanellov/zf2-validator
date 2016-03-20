<?php

/**
 * Knlv\Zf2\Validator\NotIdentical
 *
 * @link https://github.com/kanellov/zf2-validator
 * @copyright Copyright (c) 2015 Vassilis Kanellopoulos - contact@kanellov.com
 * @license https://raw.githubusercontent.com/kanellov/zf2-validator/master/LICENSE
 */

namespace Knlv\Zf2\Validator;

use Zend\Validator\Identical;

class NotIdentical extends Identical
{
    const SAME = 'same';

    public function __construct($token = null)
    {
        $this->messageTemplates[static::SAME] = 'The two given tokens match';
        parent::__construct($token);
    }

    /**
     * Returns true if and only if a token has been set and the provided value
     * does not match that token.
     *
     * @param  mixed $value
     * @param  array $context
     * @return bool
     * @throws Zend\Validator\Exception\RuntimeException
     * if the token doesn't exist in the context array
     */
    public function isValid($value, array $context = null)
    {
        $isValid  = parent::isValid($value, $context);
        $messages = $this->getMessages();

        if (array_key_exists(self::MISSING_TOKEN, $messages)) {
            return false;
        }

        // clear previous messages
        $this->abstractOptions['messages'] = array();

        if ($isValid) {
            $this->error(self::SAME);

            return false;
        }

        return true;
    }
}
