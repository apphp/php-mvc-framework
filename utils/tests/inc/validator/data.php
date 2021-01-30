<?php

/**
 * Format: 'value' => expected result true|false
 */
$prepare_data = [
    'isAlpha'       => [
        'abcdef'   => true,
        'AbcdZxc'  => true,
        '123abc'   => false,
        12345      => false,
        "-*.,/"    => false,
        'zxbcde-'  => ['expected' => true, 'allowed' => '-'],
        'zxbcdef-' => ['expected' => false, 'allowed' => ['=', '_']]
    ],
    'isNumeric'     => [
        '12345'  => true,
        '01234'  => true,
        '123abc' => false,
        12345    => true,
        "-*.,/"  => false,
        +12      => true,
        '+12'    => false,
        -1       => false,
        '+1'     => false,
        0        => true
    ],
    'isEmail'       => [
        'me@example.com'         => true,
        'me.email'               => false,
        'email@email@.com'       => false,
        'com.email@me'           => false,
        'me.email@.com'          => false,
        'me.email@example.com'   => true,
        'me.email.me@email.info' => true,
        'me.e_mail99@email.info' => true,
        'me.e_*mail@email.info'  => false
    ],
    'validateRegex' => [
        'abcDef'    => ['expected' => true, 'pattern' => '^[a-zA-Z]+$'],
        'abcDef-'   => ['expected' => true, 'pattern' => '^[a-zA-Z\-]+$'],
        'abcDef34-' => ['expected' => true, 'pattern' => '^[a-zA-Z0-9\-]+$'],
        'abcDef34_' => ['expected' => false, 'pattern' => '^[a-zA-Z\-]+$'],
    ]
];