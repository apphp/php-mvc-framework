<?php
/**
 * Format: type, value, result
 */
$prepare_data = [
    'sanitize' => [
        ['string', 'wrwer_wer', true],
        ['string', 'asd$56fgh', true],
        ['string', 'a_+sdTgg#$56fgh', true],
        ['email', "^()!#$%&'*+-/=?^_`{|}~@.[]", false],
        ['email', 'emai.lme@example.com', true],
        ['email', 'email@e+-mail.me', true],
        ['url', "http://www.domain.com/$-_!2=+-", true],
        ['alpha', 'asd@#$23423234', false],
        ['alpha', 'asdAss', true],
        ['alpha', 'abc123', false],
        ['alphanumeric', 'asdAss_+12345', false],
        ['alphanumeric', 'asdAss12345', true],
        ['number_int', '12345', true],
        ['number_int', '0123', true],
        ['number_int', '1_eRf45_)(*', true],
        ['number_float', '123', true],
        ['number_float', '+123', true],
        ['number_float', '123.12', true],
        ['number_float', '123,12', true],
        ['number_float', '123.12a', true],
        ['number_float', '123e+02', true],
        ['number_float', '0.123e+02', true],
    ],
];
	