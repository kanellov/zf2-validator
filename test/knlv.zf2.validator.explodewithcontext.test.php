<?php
require __DIR__ . '/../vendor/autoload.php';

use FUnit as fu;
use Knlv\Zf2\Validator\ExplodeWithContext;
use Zend\Validator\Callback;

// Zend\Validator\Callback uses context in isValid method in order to test

fu::setup(function () {
    $validator = new ExplodeWithContext();
    fu::fixture('validator', $validator);
});

fu::test('Test explode_with_context passes context', function () {
    $validator = fu::fixture('validator');
    $validator->setValueDelimiter(',');

    $validator->setValidator(new Callback(function ($value, $context) {
        fu::equal('value', $context, 'Assert context pass');

        return false;
    }));

    $validator->isValid('value 1,value 2,other value', 'value');
});

fu::test('Test explode_with_context validates correctly', function () {
    $validator = fu::fixture('validator');
    $validator->setValueDelimiter('|');
    $validator->setValidator(new Callback(function ($value, $context) {
        // value begins with value from context
        return 0 === strpos($value, $context);
    }));

    fu::ok(
        $validator->isValid('value 1|value 2|value 3', 'value'),
        'Assert returns true if validates'
    );

    fu::not_ok(
        $validator->isValid('value 1|other value 2|value 3', 'value'),
        'Assert returns false if one does not validate'
    );
});
