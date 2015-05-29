<?php
require __DIR__ . '/../vendor/autoload.php';

use FUnit as fu;
use Knlv\Zf2\Validator\VatNumber;

fu::test('Test getValidator returns default composed validator', function () {
    $validator = new VatNumber();
    fu::ok($validator->getValidator() instanceof Ddeboer\Vatin\Validator);
});

fu::test('Test options are set', function () {
    $validator = new VatNumber(array(
        'country'        => 'EL',
        'checkExistence' => false,
    ));
    fu::equal('EL', $validator->getCountry());
    fu::equal(false, $validator->getCheckExistence());
});

fu::test('Test throws exception on invalid country', function () {
    fu::throws(function () {
        $validator = new VatNumber(array(
            'country' => 'GR',
        ));
    }, array(), 'Zend\Validator\Exception\InvalidArgumentException');
});

fu::test('Test validates same as composed validator', function () {
    $data = array(
        array(false, 'EL', '123456789', true),
        array(false, 'BE', '0123456789', true),
        array(true, 'NL', '987654321B01', false),
        array(true, 'NL', '7654321B01', false), // should fail
        array(true, 'BE', '01234567', false), // should fail
    );

    $validator = new VatNumber();
    fu::all_ok($data, function ($values) use ($validator) {
        $validator->setCheckExistence($values[0]);
        $validator->setCountry($values[1]);

        return $validator->isValid($values[2]) === $values[3];
    });
});

fu::test('Test messages', function () {
    $validator = new VatNumber();
    $validator->isValid("EL123456789");
    fu::equal($validator->getMessages(), array('vatInvalid' => 'The VAT is invalid'));
});
