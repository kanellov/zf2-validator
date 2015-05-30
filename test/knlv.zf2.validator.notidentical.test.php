<?php
require __DIR__ . '/../vendor/autoload.php';

use FUnit as fu;
use Knlv\Zf2\Validator\NotIdentical;

fu::setup(function () {
    $validator = new NotIdentical();
    fu::fixture('validator', $validator);
});

fu::test('Test not_identical validates correctly', function () {
    $validator = fu::fixture('validator');

    $validator->setToken('token');
    fu::not_ok(
        $validator->isValid('value', array('token' => 'value')),
        'Assert validator returns false on same'
    );
    fu::ok(
        $validator->isValid('value', array('token' => 'other')),
        'Assert validator retuns true on different'
    );

    fu::ok(
        $validator->isValid('value'),
        'Assert validator returns true if no context provided'
    );

    fu::ok(
        $validator->isValid('value', array('other_token' => 'value')),
        'Assert validator returns true if token not found in context'
    );
    $validator->setToken(null);
    fu::not_ok(
        $validator->isValid('value', array('token' => 'value')),
        'Assert validator return false if no token is set'
    );
});

fu::test('Test not_identical messages', function () {
    $validator = fu::fixture('validator');

    $validator->setToken('token');
    $validator->isValid('value', array('token' => 'value'));
    fu::has($validator::SAME, $validator->getMessages(), 'Assert same message');

    $validator->isValid('value', array('token' => 'other'));
    $messages = $validator->getMessages();
    fu::ok(empty($messages), 'Assert empty messages if validator validates');

    $validator->setToken(null);
    $validator->isValid('value', array('token' => 'value'));
    fu::has(
        $validator::MISSING_TOKEN,
        $validator->getMessages(),
        'Assert missing token message'
    );
});
